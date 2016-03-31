<?php
namespace Sd\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sd\BaseBundle\Form\Transformer\GridFSUploadTypeTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class GridFSUploadType extends AbstractType
{
   public function __construct(GridFSUploadTypeTransformer $transformer)
   {
       $this->transformer = $transformer;
   }

   public function buildView(FormView $view, FormInterface $form, array $options)
   {
       $data = $form->getData();
       if ($data) {
           $view->vars['required'] = false;
       }
   }
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
       $builder->addModelTransformer($this->transformer);
   }

	public function getParent() {
        return 'file';
	}

	public function getName()
	{
	    return 'gridfs_upload_type';
	}

}