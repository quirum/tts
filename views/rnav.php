<div id="toolbar-tts">
  <a href="?display=ttsng&view=form" class="btn btn-default"><i class="fa fa-plus"></i>&nbsp;<?php echo _("Add TTS")?></a>
  <a href="?display=ttsng" class="btn btn-default"><i class="fa fa-list"></i>&nbsp;<?php echo _("List TTS")?></a>
</div>
<table data-url="ajax.php?module=ttsng&amp;command=getJSON&amp;jdata=grid" data-cache="false" data-toggle="table" data-search="true" data-toolbar="#toolbar-tts" class="table" id="table-all-side">
    <thead>
        <tr>
            <th data-sortable="true" data-field="name"><?php echo _('TTS')?></th>
        </tr>
    </thead>
</table>
<script type="text/javascript">
	$("#table-all-side").on('click-row.bs.table',function(e,row,elem){
		window.location = '?display=ttsng&view=form&id='+row['id'];
	})
</script>
