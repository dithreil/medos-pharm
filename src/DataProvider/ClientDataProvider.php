<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Model\Client\Document;

class ClientDataProvider
{
    /**
     * @param array $requiredDocuments
     * @return Document[]
     */
    public static function transformPlainDocumentToItems(array $requiredDocuments): array
    {
        $result = array();

        foreach ($requiredDocuments as $document) {
            $newDocument = new Document($document['code'], $document['name']);
            $categories = str_split($document['ctgs'], 1);

            foreach ($categories as $category) {
                $newDocument->addCategory($category);
            }
            $result[] = $newDocument;
        }

        return $result;
    }
}
