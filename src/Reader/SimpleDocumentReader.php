<?php

namespace App\Reader;

use ZipArchive;
use XMLReader;
use DOMDocument;
use App\Entity\File;
use App\Entity\Item\Story;

/**
 * @todo use a separate document reader like:
 * https://github.com/PHPOffice/PHPWord
 *
 * Reader code is currently mixed with App specific code (story entity) this should not be the case.
 * This class is supposed to be the glue using the reader and filling App entities with data received from the reader.
 */
class SimpleDocumentReader
{
    /**
     * Creates a new Story from given File.
     *
     * @param File $file The file to create a story from. This should be a document.
     *
     * @return Story
     */
    public function getDocumentAsStory(File $file): Story
    {
        $xml = $this->readZippedXml($file);

        $html = $this->xmlToHtml($file, $xml);

	$story = new Story($file->getFileName(), $html);
	$story->setFile($file);
        return $story;
    }

    /**
     * Reads XML from archived text document.
     *
     * @param File $file
     *
     * @return string
     */
    private function readZippedXml(File $file): string
    {
        // Create new ZIP archive
        $zip = new ZipArchive();
	// Open zipped archive file
	$isOpen = $zip->open($file->getSource());

	if(true === $isOpen) {
	   $index = $zip->locateName($this->getContentIdentifier($file));
	}

        // Open received archive file
        if (true === $isOpen && false !== $index) {
            // If found, read it to the string
            $content = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $doc = new DOMDocument();
            $doc->loadXML($content, LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Read XML data
            return $doc->saveXML();
        }

        // In case of failure return empty string
        return '';
    }

    /**
     * Get the xml content identifier from given file.
     * This uses a mime type match.
     *
     * @param File $file
     */
    private function getContentIdentifier(File $file): string
    {
	switch ($file->getMimeType()) {
	    case 'application/vnd.oasis.opendocument.text':
        	return 'content.xml';
      	    /**
             * @todo check why octet-stream happens on docx.
       	     * It's default MIME for unknown and could be anything.
             */
	    case 'application/octet-stream':
      	    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        	return 'word/document.xml';
    	}
	return '';
    }

    /**
     * Get the xml namespace identifier from given file.
     * This uses a mime type match.
     *
     * @param File $file
     */
    private function getNamespaceIdentifier(File $file): string
    {
        switch ($file->getMimeType()) {
	    case 'application/vnd.oasis.opendocument.text':
              // xpath('text:p/*?/text()')
              return 'text';
	    case 'application/octet-stream':
      	    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
              // xpath('w:p/*?/text()')
              return 'w';
        }
    }

    /**
     * Converts an xml string to a html string.
     *
     * @param File $file
     * @param string $xmlString Xml string to convert to supported and cleaned up HTML
     */
    private function xmlToHtml(File $file, string $xmlString): string
    {
        // Read XML string
        $reader = new XMLReader();
        $reader->xml($xmlString);

	// Initialize variables
        $text = '';
        $formatting['header'] = 0;

        // Loop through XML DOM
        while ($reader->read()) {
            // Look for new paragraphs
	    $nodeTypeCheck = XMLReader::ELEMENT === $reader->nodeType;
	    $ns = $this->getNamespaceIdentifier($file);
	    $nsCheck = $ns.':p' === $reader->name;

	    if(!$nodeTypeCheck || !$nsCheck) {
		continue;
	    }
            // Read paragraph outerXML
            $p = $reader->readOuterXML();

	    // Search for heading
            preg_match('/<'.$ns.':pStyle '.$ns.':val="Heading.*?([1-6])"/', $p, $matches);

            if (!empty($matches)) {
                $formatting['header'] = $matches[1];
            } else {
                $formatting['header'] = 0;
            }

            // Open h-tag or paragraph
            $text .= ($formatting['header'] > 0) ? '<h'.$formatting['header'].'>' : '';
	    // Concatenate content
            $text .= htmlentities(iconv('UTF-8', 'ASCII//TRANSLIT', $reader->expand()->textContent));
	    // Close h-tag or paragraph
            $text .= ($formatting['header'] > 0) ? '</h'.$formatting['header'].'>' : '<br>';
        }
        $reader->close();

        // Suppress warnings. loadHTML does not require valid HTML but still warns against it...
        // Fix invalid html
        $doc = new DOMDocument();
        $doc->encoding = 'UTF-8';
        // Load as HTML without html/body and doctype
        @$doc->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // @todo remove strip tags if allowed with contenteditable / wysiwyg implementation
        return strip_tags(simplexml_import_dom($doc)->asXML() ?? '', '<br>');
    }
}
