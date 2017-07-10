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

class ImageView extends Field {
    public $type="imageview";
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
        foreach($this->options as $opt) {
            $opts[$opt]='<i class="fa '.$opt.'"></i>';
        }
        foreach($opts as $value=>$desc) {
            if($this->value == $value) {
                $this->description=$desc;
            }
        }
    }

    public function preview($w,$h){
        $this->w=$w;
        $this->h=$h;
        return $this;
    }
    public $w=0;
    public $h=0;
    public function build() {
        $output="";
        unset($this->attributes['type'],$this->attributes['size']);
        if(parent::build() === false)
            return;

        switch ($this->status) {
            case "disabled" :
            case "show" :
            case "create" :
            case "modify" :
                $style="style='width:{$this->w}px;'";
                $output="<div class='help-block'><image $style src='".$this->value."'></div>";
                break;

            case "hidden" :
                $output=Form::hidden($this->name,$this->value);
                break;

            default :
        }
        $this->output=$output;
    }


}
