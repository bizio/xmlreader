<?php

use Controller\AppController;
use Service\XmlService;
use Service\FileUploadService;

/**
 * Description of Index
 *
 * @author Fabrizio Manunta
 */
class Index extends AppController
{
    public function post()
    {
        
        $xmlService = new XmlService();
        $xmlFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        
        switch (true) {
            
            case $this->getRequest()->getPost('xmlData'):
                $xmlFile .= 'xml_data_' . date('YdmHis') . '.xml';
                file_put_contents(
                    $xmlFile, 
                    trim($this->getRequest()->getPost('xmlData')));
                break;
            
            case $this->getRequest()->getFile('xmlFile'):
                $file = $this->getRequest()->getFile('xmlFile');
                $xmlFile .= $file['name'];
                
                try {
                
                    $uploadService = new FileUploadService($this->getRequest()->getFile('xmlFile'));
                    $uploadService->upload($xmlFile);
                    
                } catch (\Exception $e) {
                    $this->addError($e->getMessage());
                    
                }
                
                break;
            
            default :
                $this->addError('Invalid XML data');
                return;
        }
        
        try {
            
            if(!$this->hasErrors()) {
                $xmlService->saveDocument($xmlFile);
                
            }
            
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
        
    }
    
    public function get()
    {
        
    }
}

?>
