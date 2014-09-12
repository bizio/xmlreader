<?php

use Controller\AppController;
use Service\XmlService;

/**
 * Description of List
 *
 * @author Fabrizio Manunta
 */
class Documents extends AppController
{
    public function get()
    {
        $service = new XmlService();
        if($this->getRequest()->getParam('id')) {
            try {
                
                $xml = $service->getDocumentById($this->getRequest()->getParam('id'));
                /**
                 *  a bit ugly but it's just for the purpose of displaying the
                 *  xml data 
                 */
                
                header('Content-type: text/xml; charset=utf8;');
                echo $xml->toXml();
                die();
            } catch (\Exception $e) {
                $this->addError($e->getMessage());
            }
        } else {
            $documents = $service->getDocumentsList();
        
            if(!empty($documents)) {
                echo "<ul>";
                foreach($documents as $document) {
                    echo "<li><a href='/documents/?id={$document['documents_id']}' target='_new'>{$document['name']}</a></li>";
                }
                echo "</ul>";
            } else {
                echo "No documents found";
            }    
        }
                
    }
}

?>
