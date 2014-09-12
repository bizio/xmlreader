<?php
namespace Persistence\Repository;
use Persistence\Database;
use Model\XmlDocument;

/**
 * Implementation of XmlDataRepository to retrieve and persist
 * xml data in a database
 *
 * @package Persistence
 * @subpackage Repository
 * @author Fabrizio Manunta
 */
class XmlDb implements XmlDataRepository
{
    /**
     *
     * @var Database
     */
    private $_db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }
    
    /**
     * 
     * @param integer $id
     * @return array
     * @throws \Exception
     */
    public function getDocument($id, $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $selectDocument = 
            'SELECT 
                dt.documents_tags_id, d.documents_id, dt.tag_id, 
                dt.parent_tag_id, dt.tag_name, dt.value, ta.tags_attributes_id, 
                ta.attribute_name, ta.attribute_value '
            . 'FROM documents d '
            . 'LEFT JOIN documents_tags dt ON(d.documents_id = dt.document_id) '
            . 'LEFT JOIN tags_attributes ta ON(dt.documents_tags_id = ta.documents_tags_id) '
            . 'WHERE d.documents_id = :id '
            . 'ORDER BY dt.parent_tag_id, dt.tag_id'
        ;
        
        $st = $this->_db->prepare($selectDocument);
        $st->bindParam(':id', $id, \PDO::PARAM_INT);
        if(!$st->execute()) {
            $error = $st->errorInfo();
            throw new \Exception($error[2]);
        }
        
        $tags = $st->fetchAll($fetchStyle);
        
        return $tags;
    }
    
    /**
     * 
     * @param XmlDocument $document
     * @throws \Exception
     */
    public function saveDocument(XmlDocument $document)
    {
        $insertDocument = 'INSERT INTO documents (name) VALUES (:name)';
        $st = $this->_db->prepare($insertDocument);
        $st->bindValue(':name', $document->getName(), \PDO::PARAM_STR);
        $this->_db->beginTransaction();
        
        $this->_execute($st);
        
        $documentId = $this->_db->lastInsertId();
        
        /**
         * Tags
         */
        $insertTags = 'INSERT INTO documents_tags '
            . '(document_id, tag_id, parent_tag_id, tag_name, value) VALUES ';
        ;
        $values = array();
        $flatTags = $document->getTagsFlat();
        foreach ($flatTags as $tag) {
            $id = (int)$tag->getTagId();
            $values[] = "(:documentId{$id}, :tagId{$id}, :parentTagId{$id}, :tagName{$id}, :value{$id})";
        }
        
        $insertTags .= implode(', ', $values);
        
        $st = $this->_db->prepare($insertTags);
        
        foreach($flatTags as $tag) {
            $id = (int)$tag->getTagId();
            $st->bindValue(":documentId{$id}", $documentId, \PDO::PARAM_INT);
            $st->bindValue(":tagId{$id}", $tag->getTagId(), \PDO::PARAM_INT);
            $st->bindValue(":parentTagId{$id}", $tag->getParentTagId(), \PDO::PARAM_INT);
            $st->bindValue(":tagName{$id}", $tag->getTagName(), \PDO::PARAM_INT);
            $st->bindValue(":value{$id}", $tag->getValue(), \PDO::PARAM_INT);
        }
                
        $this->_execute($st);
        
        /**
         * must commit here in order to retrieve the tags ids from db
         */
        $this->_db->commit();
        
        /**
         * Save attributes
         */
        $tagsCollectionWithAttributes = array_filter(
            $flatTags, 
            function($var) {
                return $var->hasAttributes();
            }
        );
        
        /**
         * there's no attribute to save
         */
        if(empty($tagsCollectionWithAttributes)) {
            return;
        }
        
        $tags = array_filter(
            $this->getDocument($documentId),
            function($var) use ($tagsCollectionWithAttributes) {
                return array_key_exists($var['tag_id'], $tagsCollectionWithAttributes);
            }
        );
        
        $attributes = array();

        foreach($tags as $tag) {
            $tagId = (int)$tag['documents_tags_id'];
            $attributes[] = "(:documentTagsId{$tagId}, :name{$tagId}, :value{$tagId})";
        }
        
        $insertAttributes = 'INSERT INTO tags_attributes '
            . '(documents_tags_id, attribute_name, attribute_value) VALUES '
            . implode(', ', $attributes);
                
        
        $st = $this->_db->prepare($insertAttributes);
        
        foreach($tags as $tagId => $tag) {
            $tagObj = $tagsCollectionWithAttributes[$tagId];
            $tagObj->setId($tag['documents_tags_id']);
            
            foreach($tagObj->getAttributes() as $attribute) {
                
                $st->bindValue(":documentTagsId{$tagObj->getId()}", $tagObj->getId(), \PDO::PARAM_INT);
                $st->bindValue(":name{$tagObj->getId()}", $attribute->getName(), \PDO::PARAM_STR);
                $st->bindValue(":value{$tagObj->getId()}", $attribute->getValue(), \PDO::PARAM_STR);
            }
        }
        
        $this->_execute($st);
                
    }
    
    /**
     * Get the list of saved documents
     * 
     * @return array, documents list
     * @todo add pagination
     */
    public function getDocumentsList()
    {
        $selectDocuments = 'SELECT * FROM documents';
        $st = $this->_db->prepare($selectDocuments);
        
        $this->_execute($st);
        $list = $st->fetchAll(\PDO::FETCH_ASSOC);
        
        return $list;
    }
    
    /**
     * Execute a PDOStatement and throws an exception in case of any error
     * 
     * @param \PDOStatement $st
     * @throws \Exception
     */
    private function _execute(\PDOStatement $st)
    {
        if(!$st->execute()) {
            $this->_db->rollBack();
            $error = $st->errorInfo();
            throw new \Exception('Error trying to execute query "' . $st->queryString . '": ' . $error[2]);
        }
    }
}

?>
