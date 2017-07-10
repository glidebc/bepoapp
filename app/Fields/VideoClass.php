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
use App\Model\NewBepoTV\VideoClassModel;

class VideoClass extends Field {
	public $type="videoclass";
	public $description="";
	public $clause="where";
	public $options=array(
		'fa-folder-o'=>'fa-folder-o',
		'fa-folder-open-o'=>'fa-folder-open-o',
		'fa-smile-o'=>'fa-smile-o',
		'fa-folder'=>'fa-folder'
	);

	public function getValue() {
		parent::getValue();
		$vcColl=array_flip(VideoClassModel::pluck('_id','name')->toArray());
		$this->options=$vcColl;
		foreach($this->options as $value=>$desc) {
			if($this->value==$value) {
				$this->description=$desc;
			}
		}
	}

	public function build() {
		$output="";
		unset($this->attributes['type'],$this->attributes['size']);
		if(parent::build()===false)
			return;

		if('show'==$this->status||'modify'==$this->status) {
			$readonly=$this->status=='show'?'true':'false';
			$js="
$('.selectpicker').each(function(idx,el){
    $(el).find('option').each(function(){
        var val=$(this).html();
        var data_content='<i class=\'fa '+val+'\'></i>';
        $(this).attr('data-content',data_content);
    });
});
$('.selectpicker').selectpicker({
  size: 10,
  width:'auto'
});
        ";
			Rapyd::script($js);
		}

		switch ($this->status) {
			case "disabled":
			case "show":
				if(!isset($this->value)) {
					$output=$this->layout['null_label'];
				}
				else {
					$output=$this->description;
				}
				$output="<div class='help-block'>".$output."&nbsp;</div>";
				break;

			case "create":
			case "modify":
				$this->attributes['class'].=' selectpicker';
				$output=Form::select($this->name,$this->options,$this->value,$this->attributes).$this->extra_output;
				//$output=$this->build_element();
				break;

			case "hidden":
				$output=Form::hidden($this->name,$this->value);
				break;

			default:
		}
		$this->output=$output;
	}

	private function build_element() {
		foreach($this->options as $opt) {
			$opts[$opt]='<i class="fa '.$opt.'"></i>';
		}
		//$this->attributes['class']='selectpicker';
		//return Form::select($this->name,$this->options,$this->value,$this->attributes).$this->extra_output;
		$id=$this->attributes['id'];
		$desc=$this->description;
		$buff[]="<select name='$id' id='$id' class='selectpicker'>";
		foreach($opts as $value=>$desc) {
			$buff[]="<option data-content='$desc' value='$value'>$value</option>";
		}
		$buff[]='</select>';
		return implode("",$buff);
	}

}
