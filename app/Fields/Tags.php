<?php

namespace App\Fields;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\Rapyd;
use Zofe\Rapyd\DataForm\Field\Field;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\NewBepoTV\TagModel;

class Tags extends Field {

	public $type="tags";
	public function build() {
		$id=$this->name;
		if(parent::build()===false)
			return;
		$readonly=false;
		switch ($this->status) {
			case "create":
			case "modify":
				break;
			case "disabled":
			case "show":
			case "hidden":
			default:
				$readonly=true;
		}
		$output='<input readonly='.$readonly.' type="text" value="'.$this->value.'" id="'.$this->name.'" name="'.$this->name.'" />';

		$tags=array();
		foreach(TagModel::get() as $m) {
			$tags[]=$m->name;
		}
		$tagsHtml=json_encode($tags,JSON_UNESCAPED_UNICODE);
        $style=$readonly?"ul.tagit {border-style:none}":"";
		$readonly=$readonly?'true':'false';
		
		$script=<<<SCRIPT
        $(document).ready(function() {
            var url="/newbepotv_tag/autocomplete";
                $('#$id').tagit({
                availableTags: $tagsHtml,
                singleField: true,
                readOnly: $readonly
            });
        });
SCRIPT;
        Rapyd::style($style);
		Rapyd::script($script);
		$this->output="\n".$output."\n".$this->extra_output."\n";
	}

}
