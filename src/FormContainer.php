<?php declare(strict_types=1);

namespace FrontEditor;

use GeneralForm\IFormContainer;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * Class FormContainer
 *
 * @author  geniv
 * @package FrontEditor
 */
class FormContainer implements IFormContainer
{
    use SmartObject;


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $form->addTextArea('content', 'front-editor-content');

        $form->addSubmit('send', 'front-editor-send');
    }
}
