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

    /** @var string */
    private $type = 'textarea';


    /**
     * Set type.
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        switch ($this->type) {
            case 'text':
                $form->addText('content', 'front-editor-content');
                break;

            case 'textarea':
                $form->addTextArea('content', 'front-editor-content');
                break;

            case 'editor':
                $form->addTextArea('content', 'front-editor-content');
                break;
        }
        $form->addSubmit('send', 'front-editor-send');
    }
}
