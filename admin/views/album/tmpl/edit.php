<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select', null, array('disable_search_threshold' => 0 ));
JHtml::_('jquery.ui', array('core', 'sortable'));

$doc->addStylesheet(jURI::base(true) . '/components/com_speasyimagegallery/assets/css/font-awesome.min.css');
$doc->addStylesheet(jURI::base(true) . '/components/com_speasyimagegallery/assets/css/style.css');
$doc->addScript(jURI::base(true) . '/components/com_speasyimagegallery/assets/js/validation.js');
$doc->addScript(jURI::base(true) . '/components/com_speasyimagegallery/assets/js/script.js');

// Language strings
JText::script('COM_SPEASYIMAGEGALLERY_DELETE_IMAGE_CONFIRM');
JText::script('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOADING');
JText::script('COM_SPEASYIMAGEGALLERY_MODAL_EDIT_IMAGE');

if($this->item->id) {
  $doc->addScriptdeclaration('var album_id = '. $this->item->id .';');
} else {
  $doc->addScriptdeclaration('var album_id = 0;');
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_speasyimagegallery&layout=edit&id=' . (int) $this->item->id); ?>"
  method="post" name="adminForm" id="adminForm" class="form-validate">
  <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

  <div class="form-horizontal">

    <div class="row-fluid">
      <div class="span9">
        <?php
        if($this->item->id)
        {
          echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'images'));
          echo JHtml::_('bootstrap.addTab', 'myTab', 'images', 'Images');
          echo JLayoutHelper::render('images', array('total'=>$this->total, 'images'=>$this->images));
          echo JHtml::_('bootstrap.endTab');
        }
        else
        {
          echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'description'));
        }
        ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', 'Description'); ?>
        <div>
          <?php echo $this->form->getInput('description'); ?>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', 'Publishing'); ?>
        <div>
          <?php echo $this->form->renderFieldset('publishing'); ?>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
      </div>

      <div class="span3">
        <fieldset class="form-vertical">
          <?php echo $this->form->renderFieldset('info'); ?>
        </fieldset>
      </div>
    </div>

  </div>

  <input type="hidden" name="task" value="album.edit" />
  <?php echo JHtml::_('form.token'); ?>
</form>
