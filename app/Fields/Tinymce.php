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

class Tinymce extends Field {

	public $type = "text";
	public function build() {
		$output = "";
		if (parent::build() === false)
			return;
		switch ($this->status) {
			case "disabled" :
			case "show" :
				if ($this -> type == 'hidden' || $this -> value == "") {
					$output = "";
				}
				elseif ((!isset($this -> value))) {
					$output = $this -> layout['null_label'];
				}
				else {
					//$output = nl2br(htmlspecialchars($this -> value));
				}
				$output = Form::textarea($this -> name, $this -> value, $this -> attributes);
				break;
				//$output = "<div class='help-block'>" . $output . "&nbsp;</div>";
				break;
			case "create" :
			case "modify" :
				$output = Form::textarea($this -> name, $this -> value, $this -> attributes);
				break;
			case "hidden" :
				$output = Form::hidden($this -> name, $this -> value);
				break;
			default :
				;
		}
         if('show'== $this->status || 'modify' == $this->status ){
            $readonly=$this->status=='show'?'true':'false';
            $script=<<<SCRIPT
                tinymce.init(
                    {   selector: "#{$this->name}",
                        //menubar: 'file edit insert view format table tools',
                        language:'zh_TW',
                        subfolder:"",
                        plugins: [
                             "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                             "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                             "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                       ],
                       toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                       toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
                       image_advtab: true ,

                       external_filemanager_path:"/filemanager/",
                       filemanager_title:"Responsive Filemanager" ,
                    }
                );
SCRIPT;
            Rapyd::script($script);
        }
		$this -> output = "\n" . $output . "\n" . $this -> extra_output . "\n";
	}

}
