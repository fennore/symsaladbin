<?php

namespace App\Reader;

use App\Entity\{File,Item\Story};
use DOMDocument;
use XMLReader;
use ZipArchive;

/**
 * @todo use a separate document reader like:
 * https://github.com/PHPOffice/PHPWord
 */
class SimpleDocumentReader
{

    public function getDocument(File $file): DOMDocument
    {
        $document = $this->readFile($file);

        return $this->extractHTMLContent($document, $this->getNamespaceIdentifier($file));
    }

    private function readFile(File $file): DOMDocument
    {

        $zip = new ZipArchive();
        $isOpen = $zip->open($file->getSource());

        $doc = new DOMDocument();
        
        if (true === $isOpen) {
            $index = $zip->locateName($this->getContentIdentifier($file));
        }

        if (true !== $isOpen || false === $index) {
            return $doc;
        }

        $content = $zip->getFromIndex($index);

        $zip->close();

        // Skip errors and warnings when loading the string content into DOMDocument
        $doc->loadXML($content, LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);

        return $doc;
    }

    /**
     * Get the xml content identifier from given file.
     * This uses a mime type match.
     */
    private function getContentIdentifier(File $file): string
    {
        switch ($file->getMimeType()) {
            case 'application/vnd.oasis.opendocument.text':
                return 'content.xml';
            /*
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

    private function extractHTMLContent(DOMDocument $document, $namespace): DOMDocument
    {
        $reader = new XMLReader();
        $reader->xml($document->saveXML());

        $text = '';
        $formatting['header'] = 0;

        while ($reader->read()) {
            // Look for new paragraphs
            $nodeTypeCheck = XMLReader::ELEMENT === $reader->nodeType;
            $nsCheck = "{$namespace}:p" === $reader->name;

            if (!$nodeTypeCheck || !$nsCheck) {
                continue;
            }
            // Read paragraph outerXML
            $p = $reader->readOuterXML();

            // Search for heading
            preg_match('/<'.$namespace.':pStyle '.$namespace.':val="Heading.*?([1-6])"/', $p, $matches);

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
        $newDocument = new DOMDocument();
        $newDocument->encoding = 'UTF-8';
        // Load as HTML without html/body and doctype
        @$newDocument->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        return $doc;
    }
}
