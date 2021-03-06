<?php
if(!defined('OSTCLIENTINC') || !$category || !$category->isPublic()) die('Access Denied');
?>
<div class="container topheader">
<div class="row">
<div class="span8">
    <h2><strong><?php echo $category->getLocalName() ?></strong></h2>
<p>
<?php echo Format::safe_html($category->getLocalDescriptionWithImages()); ?>
</p>
<hr>
<div class="panel panel-default faqlist">
<?php
$faqs = FAQ::objects()
    ->filter(array('category'=>$category))
    ->exclude(array('ispublished'=>FAQ::VISIBILITY_PRIVATE))
    ->annotate(array('has_attachments' => SqlAggregate::COUNT(SqlCase::N()
        ->when(array('attachments__inline'=>0), 1)
        ->otherwise(null)
    )))
    ->order_by('-ispublished', 'question');

if ($faqs->exists(true)) {
    echo '
    <div class="panel-heading">
         <h2 class="panel-title">'.__('Frequently Asked Questions').'</h2>
         </div>
      <div class="panel-body">
         <div id="faq">
            <ol>';
foreach ($faqs as $F) {
        $attachments=$F->has_attachments?'<span class="Icon file"></span>':'';
        echo sprintf('
            <li><a href="faq.php?id=%d" >%s &nbsp;%s</a></li>',
            $F->getId(),Format::htmlchars($F->question), $attachments);
    }
    echo '  </ol>
         </div>
         </div>
         <div class="panel-footer">
                 <a class="back" href="index.php">&laquo; '.__('Go Back').'</a></div>';
}else {
    echo '<strong>'.__('This category does not have any FAQs.').' <a href="index.php">'.__('Back To Index').'</a></strong>';
}
?>
</div>
</div>
</div>

<div class="span4">
    <div class="sidebar">

    <div class="content">
        <section>
            <div class="header"><?php //echo __('Help Topics'); ?></div>
<?php
foreach (Topic::objects()
    ->filter(array('faqs__faq__category__category_id'=>$category->getId()))
    as $t) { ?>
        <a href="?topicId=<?php echo urlencode($t->getId()); ?>"
            ><?php echo $t->getFullName(); ?></a>
<?php } ?>
        </section>
    </div>
    </div>
</div>
</div>
