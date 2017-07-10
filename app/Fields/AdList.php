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
use App\Model\Bepoapp\Ad;
use App\Model\Bepoapp\AdDetail;

class AdList extends Field {
	public $type="adlist";
	public $description="";
	public $clause="where";

	public function getValue() {
		parent::getValue();
	}

	public function build() {
		$output="";
		if(parent::build()===false)
			return;

		switch ($this->status) {
			case "disabled":
			case "show":
			case "create":
			case "modify":
				//$output="<div class='help-block'><image $style src='".$this->value."'></div>";
				$id=$this->value;
				$adModel=Ad::findOrFail($id);
				$datalist=array( array(
						'時間',
						'曝光',
						'點擊',
						'點擊率'
					));
                $show=0;
                $click=0;
				foreach(AdDetail::where('ad_id','=',$id)->orderBy('date','asc')->get() as $data) {
					$datalist[]=array(
						$data->date,
						number_format($data->show),
						number_format($data->click),
						$data->delta
					);
                    $show+=$data->show;
                    $click+=$data->click;
				}
                $delta='';
                if($click>0){
                    $delta=$click/$show*100;
                    $delta=round($delta,2);
                    if($delta>0){
                        $delta.='%';
                    }else{$delta='';}
                }

				$datalist[]=array(
					'總計',
					number_format($show),
					number_format($click),
					$delta
				);
                $output=view('fields.adlist',array('datalist'=>$datalist));
				break;
			case "hidden":
				$output=Form::hidden($this->name,$this->value);
				break;

			default:
		}
		$this->output=$output;
	}

}
