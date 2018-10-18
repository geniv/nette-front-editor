<?php declare(strict_types=1);

namespace FrontEditor;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;
use Nette\Utils\Callback;


/**
 * Class FrontEditor
 *
 * @author  geniv
 * @package FrontEditor
 */
class FrontEditor extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var ITranslator */
    private $translator;
    /** @var string */
    private $templatePath, $templatePathLink;
    /** @var bool */
    private $acl = false;
    /** @var bool */
    private $editor = false;
    /** @var array */
    private $data;
    /** @var callable */
    public $onSuccess, $onLoadData;
    /** @var array */
    private $variableTemplate = [];
    /** @var string @persistent */
    public $identification = null;


    /**
     * FrontEditor constructor.
     *
     * @param IFormContainer   $formContainer
     * @param ITranslator|null $translator
     */
    public function __construct(IFormContainer $formContainer, ITranslator $translator = null)
    {
        parent::__construct();

        $this->formContainer = $formContainer;
        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/FrontEditor.latte';   // set path
        $this->templatePathLink = __DIR__ . '/FrontEditorLink.latte';   // set path
    }


    /**
     * Get form container.
     *
     * @return IFormContainer
     */
    public function getFormContainer(): IFormContainer
    {
        return $this->formContainer;
    }


    /**
     * Set template path.
     *
     * @param string $path
     */
    public function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
    }


    /**
     * Set template path link.
     *
     * @param string $path
     */
    public function setTemplatePathLink(string $path)
    {
        $this->templatePathLink = $path;
    }


    /**
     * Add variable template.
     *
     * @param string $name
     * @param        $values
     */
    public function addVariableTemplate(string $name, $values)
    {
        $this->variableTemplate[$name] = $values;
    }


    /**
     * Set template.
     *
     * @internal
     * @param ITemplate $template
     */
    private function setTemplate(ITemplate $template)
    {
        $data = $this->getData();   // load data
        // add user defined variable
        foreach ($this->variableTemplate as $name => $value) {
            $template->$name = $value;
        }

        $template->acl = $this->acl;
        $template->editor = $this->editor;
        $template->data = $data;
        $template->identification = $this->identification;
    }


    /**
     * Get data.
     *
     * @internal
     * @return array
     */
    private function getData(): array
    {
        $result = $this->data;
        if ($this->onLoadData) {
            $result = Callback::invokeSafe($this->onLoadData, [$this->identification], null);
        }
        return $result;
    }


    /**
     * Render link.
     *
     * @param string $identification
     */
    public function renderLink(string $identification)
    {
        $this->identification = $identification;

        $template = $this->getTemplate();
        $this->setTemplate($template);

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePathLink);
        $template->render();
    }


    /**
     * Render.
     */
    public function render()
    {
        $template = $this->getTemplate();
        $this->setTemplate($template);

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }


    /**
     * Set ACL.
     *
     * @param bool $acl
     */
    public function setAcl(bool $acl)
    {
        $this->acl = $acl;
    }


    /**
     * Set data.
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }


    /**
     * Handle switch editor.
     *
     * @param bool        $state
     * @param string|null $identification
     */
    public function handleSwitchEditor(bool $state, string $identification = null)
    {
        $this->editor = $state;
        $this->identification = $identification;

        if ($this->presenter->isAjax()) {
            $this->redrawControl('content');
        }
    }


    /**
     * Create component form.
     *
     * @param string $name
     * @return Form
     */
    protected function createComponentForm(string $name): Form
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);
        $form->addHidden('id');

        $data = $this->getData();   // load data
        $this->formContainer->getForm($form);

        // set data to form
        $form->setDefaults($data);

        $form->onSuccess = $this->onSuccess;
        return $form;
    }
}
