<?php

namespace App\Reader;

use ZipArchive;
use XMLReader;
use App\Entity\File;
use App\Entity\Item\Story;

class SimpleDocumentReader
{
    /**
     * Creates a new Story from given File.
     *
     * @param File $file The file to create a story from. This should be a document.
     *
     * @return Story
     */
    public function saveDocumentAsStory(File $file)
    {
        $xml = $this->readZippedXml($file);

        return new Story();
        //$this->xmlToHtml($xml);
    }

    /**
     * Reads XML from archived text document.
     *
     * @param File $file
     *
     * @return string
     */
    private function readZippedXml(File $file)
    {
        // Create new ZIP archive
        $zip = new ZipArchive();

        switch ($file->getMimeType()) {
      case 'odt':
        $dataFile = 'content.xml';
        break;
      case 'docx':
        $dataFile = 'word/document.xml';
        break;
    }

        // Open received archive file
        if (true === $zip->open($file->getSource()) && false !== ($zip->locateName($dataFile))) {
            // If done, search for the data file in the archive
            $index = $zip->locateName($dataFile);
            // If found, read it to the string
            $data = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $doc = new DOMDocument();
            $doc->loadXML($data, LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Read XML data
            return $doc->saveXML();
        }

        // In case of failure return empty string
        return '';
    }

    private function xmlToHtml($xmlString)
    {
        // Read XML string
        $reader = new XMLReader();
        $reader->xml($xmlString);

        switch ($this->ext) {
            case 'odt':
              // xpath('text:p/*?/text()')
              $ns = 'text';
              break;
            case 'docx':
              // xpath('w:p/*?/text()')
              $ns = 'w';
              break;
        }

        // set up variables for formatting
        $text = '';
        $formatting['header'] = 0;

        // loop through docx xml dom
        while ($reader->read()) {
            // look for new paragraphs
            if (XMLREADER::ELEMENT == $reader->nodeType && $reader->name === $ns.':p') {
                // read paragraph outerXML
                $p = $reader->readOuterXML();

                // search for heading
                preg_match('/<'.$ns.':pStyle '.$ns.':val="Heading.*?([1-6])"/', $p, $matches);

                if (!empty($matches)) {
                    $formatting['header'] = $matches[1];
                } else {
                    $formatting['header'] = 0;
                }

                // open h-tag or paragraph
                $text .= ($formatting['header'] > 0) ? '<h'.$formatting['header'].'>' : '';

                $text .= htmlentities(iconv('UTF-8', 'ASCII//TRANSLIT', $reader->expand()->textContent));

                $text .= ($formatting['header'] > 0) ? '</h'.$formatting['header'].'>' : '<br>';
            }
        }
        $reader->close();

        // suppress warnings. loadHTML does not require valid HTML but still warns against it...
        // fix invalid html
        $doc = new DOMDocument();
        $doc->encoding = 'UTF-8';
        // Load as HTML without html/body and doctype
        @$doc->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // @todo remove strip tags if allowed with contenteditable / wysiwyg implementation
        return strip_tags(simplexml_import_dom($doc)->asXML(), '<br>');
    }
}
