<?php
namespace Model;

/**
 * Description of XmlTag
 *
 * @author Fabrizio Manunta
 */
class XmlTag
{
    /**
     *
     * @var integer $_id, tag primary key (auto generated)
     */
    private $_id;
    /**
     *
     * @var integer $_documentId, reference to the document owner of the tag
     */
    private $_documentId;
    
    /**
     *
     * @var integer $_tagId, unique within the document
     */
    private $_tagId;
    
    /**
     *
     * @var integer $_parentTagId, reference to the parent tag
     */
    private $_parentTagId;
    
    /**
     *
     * @var string $_tagName
     */
    private $_tagName;
    
    /**
     *
     * @var integer|string $_value
     */
    private $_value;
    
    /**
     *
     * @var ArrayObject $_attributes, collection of XmlAttributes
     */
    private $_attributes;
    
    /**
     *
     * @var ArrayObject $_children, collection of XmlTag
     */
    private $_children;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_children = new \ArrayObject();
        $this->_attributes = new \ArrayObject();
        $this->_parentTagId = 0;
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
     * @return integer
     */
    public function getDocumentId()
    {
        return $this->_documentId;
    }

    /**
     * 
     * @param integer $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->_documentId = $documentId;
    }

    /**
     * 
     * @return integer
     */
    public function getTagId()
    {
        return $this->_tagId;
    }

    /**
     * 
     * @param integer $tagId
     */
    public function setTagId($tagId)
    {
        $this->_tagId = $tagId;
    }

    /**
     * 
     * @return integer
     */
    public function getParentTagId()
    {
        return $this->_parentTagId;
    }

    /**
     * 
     * @param integer $parentTagId
     */
    public function setParentTagId($parentTagId)
    {
        $this->_parentTagId = $parentTagId;
    }

    /**
     * 
     * @return string
     */
    public function getTagName()
    {
        return $this->_tagName;
    }

    /**
     * 
     * @param string $tagName
     */
    public function setTagName($tagName)
    {
        $this->_tagName = $tagName;
    }

    /**
     * 
     * @return integer|string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * 
     * @param integer|string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * 
     * @return ArrayObject
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * 
     * @param \ArrayObject $children
     */
    public function setChildren(\ArrayObject $children)
    {
        $this->_children = $children;
    }
    
    /**
     * 
     * @return ArrayObject, collection of XmlAttribute
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * 
     * @param \ArrayObject $attributes
     */
    public function setAttributes(\ArrayObject $attributes)
    {
        $this->_attributes = $attributes;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasAttributes()
    {
        return count($this->_attributes) > 0;
    }

    /**
     * 
     * @param XmlAttribute $attribute
     */
    public function addAttribute(XmlAttribute $attribute)
    {
        $this->_attributes->append($attribute);
    }
    
    /**
     * 
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->_children) > 0;
    }
        
    /**
     * Add a child tag
     * @param XmlTag $tag
     */
    public function addChild(XmlTag $tag)
    {
        $tag->setParentTagId($this->getTagId());
        $this->_children->append($tag);
        
    }
    
    /**
     * Get a child tag by id looping through the childlren collection
     * 
     * @param type $id
     * @return type
     */
    public function getChildByTagId($id)
    {
        $elem = null;
        foreach($this->_children as $child) {
            if($id == $child->getTagId()) {
                $elem = $child;
            } elseif ($child->hasChildren()) {
                $elem = $child->getChildByTagId($id);
            }
        }
        
        return $elem;
    }

    /**
     * Returns an array containing all the children without tree structure
     * 
     * @return array
     */
    public function getChildrenFlat()
    {
        $children = array();
        foreach($this->_children as $child) {
            $children[] = $child;
            if($child->hasChildren()) {
                $children = array_merge($children, $child->getChildrenFlat());
            }
        }
        
        return $children;
    }

    /**
     * Recreates the original xml tag
     * 
     * @return string
     */
    public function toXml()
    {
        $xmlString = '<' . $this->getTagName();
        if($this->hasAttributes()) {
            foreach($this->_attributes as $attribute) {
                $xmlString .= ' ' . $attribute->getName() . '="' . $attribute->getValue() . '" ';
            }
        }
        $xmlString .= '>';
        if($this->hasChildren()) {
            foreach($this->_children as $child) {
                $xmlString .= $child->toXml();
            }
        } else {
            $xmlString .= $this->getValue();
        }
        
        $xmlString .= '</' . $this->getTagName() . '>';
        return $xmlString;
    }
}

?>
