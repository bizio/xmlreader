<?php

namespace Model;

/**
 * XmlDocument Model
 *
 * @package Model
 * @author Fabrizio Manunta
 */
class XmlDocument
{

    /**
     *
     * @var integer $_id, document primary key (auto generated)
     */
    private $_id;
    
    /**
     *
     * @var string $name, document original file name 
     */
    private $_name;
    
    /**
     *
     * @var ArrayObject $_tags, collection of XmlTag
     */
    private $_tags;

    /**
     * 
     * Constructor
     */
    public function __construct()
    {
        $this->_tags = new \ArrayObject();
    }

    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * 
     * @param integer $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Add a tag to the document
     * @param XmlTag $tag, element to add
     * @param bool $generateTree, if set to true recreates the original tree structure
     */
    public function addTag(XmlTag $tag, $generateTree = false)
    {
        if($generateTree) {
            $parent = $this->getTagByTagId($tag->getParentTagId());
            if($parent instanceof XmlTag) {
                $parent->getChildren()->append($tag);
            } else {
                $this->_tags->append($tag);
            }
            
        } else {
            $this->_tags->append($tag);
        }
        
    }
        
    /**
     * Get a tag by id looping through the tags and their childlren
     * 
     * @param integer $id
     * @return null|XmlTag
     */
    public function getTagByTagId($id)
    {
        $elem = null;
        foreach($this->_tags as $tag) {
            if($id == $tag->getTagId()) {
                $elem = $tag;
            } elseif($tag->hasChildren()) {
                $elem = $tag->getChildByTagId($id);
                
            }
        }
        
        return $elem;
    }
    
    /**
     * Returns an array containing all the tags without tree structure
     * @return array
     */
    public function getTagsFlat()
    {
        $tags = array();
        foreach($this->_tags as $tag) {
            $tags[] = $tag;
            if($tag->hasChildren()) {                
                $tags = array_merge($tags, $tag->getChildrenFlat());
            }
        }
        
        array_walk($tags, function($elem) { $elem->setChildren(new \ArrayObject()); });
        
        return $tags;
    }
    
    /**
     * Recreates the original xml from the tags
     * @return string
     */
    public function toXml()
    {
        $xmlString = '';
        if($this->hasTags()){

            $xmlString .= '<?xml version="1.0"?>';
            foreach($this->_tags as $tag) {
                $xmlString .= $tag->toXml();
            }
        }
        return $xmlString;
    }

    /**
     * 
     * @return bool
     */
    public function hasTags()
    {
        return count($this->_tags) > 0;
    }
}

?>
