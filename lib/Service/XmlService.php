<?php

namespace Service;

use Model\XmlDocument;
use Model\XmlTag;
use Model\XmlAttribute;
use Persistence\Repository\XmlDb;

/**
 * Description of XmlService
 *
 * @author Fabrizio Manunta
 */
class XmlService
{

    /**
     * @type \XMLReader $_reader
     */
    private $_reader;
    
    /**
     *
     * @var XmlDocument $_xmlDocument
     */
    private $_xmlDocument;
    
    /**
     * Keep the count of the tags in the document
     * @var integer $_tagCounter
     */
    private $_tagCounter;
    
    /**
     * Xml documents database repository
     * @var XmlDb $_repository
     */
    private $_repository;

    /**
     * 
     * Constructor
     */
    public function __construct()
    {
        $this->_reader = new \XMLReader();
        $this->_xmlDocument = new XmlDocument();
        $this->_tagCounter = 0;
        $this->_repository = new XmlDb();
    }

    /**
     * Fetch document by id
     * @param integer $id
     * @throws Exception
     */
    public function getDocumentById($id)
    {
        $tags = $this->_repository->getDocument((int)$id);
        
        if(empty($tags)) {
            throw new \Exception('Document not found');
        }
        
        $xml = new XmlDocument();
        $xml->setId($id);
        
        foreach($tags as $tag) {
            $tagObj = new XmlTag();
            $tagObj->setId($tag['documents_tags_id']);
            $tagObj->setDocumentId($tag['documents_id']);
            $tagObj->setTagId($tag['tag_id']);
            $tagObj->setParentTagId($tag['parent_tag_id']);
            $tagObj->setTagName($tag['tag_name']);
            $tagObj->setValue($tag['value']);
            
            /**
             * we have only one attribute per tag
             */
            if(!is_null($tag['attribute_name'])) {
                $attribute = new XmlAttribute();
                $attribute->setId($tag['tags_attributes_id']);
                $attribute->setDocumentsTagId($tagObj->getId());
                $attribute->setName($tag['attribute_name']);
                $attribute->setValue($tag['attribute_value']);
                $tagObj->addAttribute($attribute);
            }
            
            $xml->addTag($tagObj, true);
        }
        
        return $xml;
    }
    
    /**
     * Parse an xml file and saves it to the db keeping the original
     * tree structure
     * 
     * @param string $xmlFile, xml file name
     * @return bool
     */
    public function saveDocument($xmlFile)
    {
        
        $this->_xmlDocument->setName(basename($xmlFile));
        
        if (!$this->_reader->open($xmlFile)) {
            throw new \Exception('Invalid XML');
        }
        
        while ($this->_reader->read()) {

            /**
             * Looks for the document's root element
             */
            if ($this->_reader->nodeType == \XMLReader::ELEMENT) {
                $xmlTag = new XmlTag();
                $xmlTag->setTagName($this->_reader->name);
                $xmlTag->setTagId($this->_tagCounter);
                
                /**
                 * We're only storing the first attribute if present
                 */
                if($this->_reader->moveToFirstAttribute()) {
                    $attr = new XmlAttribute();
                    $attr->setTagId($xmlTag->getTagId());
                    $attr->setName($this->_reader->name);
                    $this->_reader->next();
                    $attr->setValue($this->_reader->value);
                    $xmlTag->addAttribute($attr);
                }
                $this->_xmlDocument->addTag($xmlTag);
                $this->_tagCounter++;
                $this->_parse($xmlTag);
            }

        }
        
        if(!$this->_xmlDocument->hasTags()) {
            throw new \Exception('The document is empty');
        }
        
        return $this->_repository->saveDocument($this->_xmlDocument);

    }

    /**
     * Creates a XmlTag object and for each xml tag found in the document, 
     * this function is called recursively to make sure we add all tags and their
     * children
     * 
     * @param XmlTag $tag
     * @return XmlTag
     */
    private function _parse(XmlTag $tag)    
    {
        while ($this->_reader->read() 
            && $this->_reader->nodeType !== \XMLReader::END_ELEMENT) {

            if ($this->_reader->nodeType == \XMLReader::ELEMENT) {
                
                $newTag = new XmlTag();
                $newTag->setTagName($this->_reader->name);
                $newTag->setTagId($this->_tagCounter);
                if($this->_reader->moveToFirstAttribute()) {
                    $attr = new XmlAttribute();
                    $attr->setName($this->_reader->name);
                    $attr->setValue($this->_reader->value);
                    $newTag->addAttribute($attr);
                }
                $tag->addChild($newTag);
                $this->_tagCounter++;   

                $this->_parse($newTag);
                                
            } elseif ($this->_reader->nodeType == \XMLReader::TEXT ||
                $this->_reader->nodeType == \XMLReader::CDATA) {
                
                $lastTag = $this->_xmlDocument->getTagByTagId($tag->getTagId());
                
                if (!$lastTag instanceof XmlTag) {
                    // something went very wrong
                    throw new \Exception('Impossible to find tag with id: ', $tag->getId());
                }
                
                $tag->setValue($this->_reader->value);
            }
            
        }
        
        return $tag;
    }

    /**
     * 
     * @return \ArrayObject
     */
    public function getDocumentsList()
    {
        return $this->_repository->getDocumentsList();
    }
}

?>
