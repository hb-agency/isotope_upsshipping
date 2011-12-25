
<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<?php if($this->message): ?>
<p class="message empty"><?php echo $this->message; ?></p>
<?php else: ?>
<?php if(count($this->trackingResults)): ?>
<?php foreach($this->trackingResults as $result): ?>
<div class="trackingNumber"><?php echo $this->trackingNumberLabel; ?>: <?php echo $result['general_info']['tracking_number']; ?></div>
<?php if($result['status']=='error'): ?>
<div class="message error"><?php echo $result['description']; ?></div>
<?php else: ?>
<table cellpadding="0" cellspacing="0">
<tbody>
<tr class="summary">
<td>
&nbsp;
</td>
</tr>
</tbody>
</table>
<?php if(count($result['activity'])): ?>
<table cellpadding="0" cellspacing="0" summary="<?php echo $this->summary; ?>">
<thead>
	<tr class="header">
		<td class="col_first datim"><?php echo $this->datimLabel; ?></td>
        <td class="activity"><?php echo $this->activityLabel; ?></td>
		<td class="location"><?php echo $this->locationLabel; ?></td>
		<td class="details"><?php echo $this->detailsLabel; ?></td>
	</tr>
</thead>
<tbody>
<?php $i=0; ?>
<?php foreach($result['activity'] as $activity): ?>
	<tr class="result <?php echo ($i%2==0 ? 'even' : 'odd'); ?>">
		<td class="col_1 datim"><?php echo $activity['datim']; ?></td>
        <td class="col_2 activity"><?php echo $activity['activity']; ?></td>
        <td class="col_3 location"><?php echo $activity['location']; ?></td>
        <td class="col_5 details"><?php echo $activity['details']; ?></td>
    </tr>
<?php $i++; ?>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<form action="<?php echo $this->action; ?>" id="<?php echo $this->formId; ?>" method="post" enctype="<?php echo $this->enctype; ?>">
<div class="formbody">
<input type="hidden" name="FORM_SUBMIT" value="<?php echo $this->formSubmit; ?>" />
<input type="hidden" name="REQUEST_TOKEN" value="{{request_token}}" />
<?php echo $this->trackingWidget; ?>
</div>
<div class="clear">&nbsp;</div>
<div class="submit_container">
		<input type="submit" class="submit <?php echo $this->class; ?> button" name="submit" value="<?php echo $this->slabel; ?>" />
</div>
</form>
</div>
<?php endif; ?>
</div>
<!-- indexer::continue -->