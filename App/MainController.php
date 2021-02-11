<?php

namespace App;

/**
 * MainController 
 * This controller contains methods for pages 
 */
class MainController extends Controller {

    /**
     * Method page - simple (static) page.
     * @param void
     */
    public function page() {
        if(isset($this->page['content'])) {
            $this->content['html'] = $this->page['content'];
            $this->response['status'] = $this->statusSuccess;
        }
    }

    /**
     * Method error404 - show 404 error page
     * @param void
     */
    public function error404() {
        $this->template->set( _ROOT_TPL_ . 'error404.html');

        $tplVars = [
            'HDR_ERROR' => $this->locale['ERR_404'], 
            'TXT_ERROR' => $this->locale['TXT_SOMETHING_WRONG']
        ];

        $this->template->setVars( $tplVars );

        $this->content['html'] = $this->template->parse();
        $this->response['status'] = $this->statusSuccess;
    }
}
