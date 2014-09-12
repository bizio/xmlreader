<?php
namespace Model;

/**
 * Description of XmlAttribute
 *
 * @author Fabrizio Manunta
 */
class XmlAttribute
{
    /**
     *
     * @var integer $_id, attribute primary key (auto generated)
     */
    private $_id;
    
    /**
     *
     * @var integer $documentsTagId, references to the tag owner of the attribute
     */
    private $_documentTagsId;
    
    /**
     *
     * @var string $_name, attribute name
     */
    private $_name;
    
    /**
     *
     * @var string $_value, attribute value
     */
    private $_value;
    
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
    public function getDocumentsTagId()
    {
        return $this->_documentTagsId;
    }

    /**
     * 
     * @param integer $documentsTagId
     */
    public function setDocumentsTagId($documentsTagId)
    {
        $this->_documentTagsId = $documentsTagId;
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
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * 
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    

}

?>
