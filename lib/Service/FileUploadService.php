<?php

namespace Service;

/**
 * Description of FileUpload
 *
 * @author Fabrizio Manunta
 */
class FileUploadService
{
    /**
     *
     * @var string $_name, original file name of the uploaded file
     */
    private $_name;
    
    /**
     *
     * @var string $_type, file type
     */
    private $_type;
    
    /**
     *
     * @var integer $_size, file size
     */
    private $_size;
    
    /**
     *
     * @var string $_tmpName, location of the temporary file
     */
    private $_tmpName;
    
    /**
     *
     * @var integer $_error
     */
    private $_error;

    /**
     * Constructor, checks for errors and initialize class properties
     *  
     * @param array $file, $_FILES[XXX] content
     * @throws \Exception
     */
    public function __construct(array $file)
    {
        $this->_error = (int) $file['error'];
        
        if($this->_hasErrors()) {
            $error = $this->_getErrorMessage();
            throw new \Exception(sprintf('File upload error: %s', $error));
        }
        
        $this->_name = $file['name'];
        $this->_type = $file['type'];
        $this->_size = $file['size'];
        $this->_tmpName = $file['tmp_name'];
        
    }
    
    /**
     * Upload a file to the specified destination
     * 
     * @param string $destination
     * @throws Exception
     * @todo check destination for permissions
     */
    public function upload($destination)
    {
        $destination = trim($destination);
        if(empty($destination)) {
            throw new \Exception('File destination cannot be empty');
        }
        
        return move_uploaded_file($this->_tmpName, $destination);
    }
    
    /**
     * 
     * @return bool
     */
    private function _hasErrors()
    {
        return $this->_error !== UPLOAD_ERR_OK;
    }

    /**
     * 
     * @return string
     */
    private function _getErrorMessage()
    {
        $message = '';
        switch ($this->_error) {
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                break;
            case UPLOAD_ERR_PARTIAL;
                $message = 'The uploaded file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE;
                $message = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing a temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'A PHP extension stopped the file upload.';
                break;
        }
        
        return $message;
    }
        
}



?>
