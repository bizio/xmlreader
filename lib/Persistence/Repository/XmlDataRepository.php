<?php
namespace Persistence\Repository;
use Model\XmlDocument;

/**
 *
 * Interface for the XmlReader
 * 
 * @package Persistence
 * @subpackage Repository
 * @author Fabrizio Manunta
 */
interface XmlDataRepository
{
    /**
     * Get a document by documentId
     * 
     * @param integer $id
     * @return array
     */
    public function getDocument($documentId);
    
    /**
     * Saves the xml document
     * 
     * @param XmlDocument $document
     */
    public function saveDocument(XmlDocument $document);
}

?>
