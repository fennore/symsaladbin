<?php

namespace App\Transformer;

use App\Entity\File;
use App\Reader\SimpleDocumentReader;

final class FileToStory
{
    public function __construct(
        private SimpleDocumentReader $documentReader
    )
    {}

    public function create(File $file): Story
    {
        $document = $this->documentReader->getDocument($file);
        // @todo remove strip tags if allowed with contenteditable / wysiwyg implementation
        return strip_tags(simplexml_import_dom($doc)->asXML() ?? '', '<br>');
    }
}
