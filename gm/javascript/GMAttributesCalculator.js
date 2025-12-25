/* GMAttributesCalculator.js <?php
 #   --------------------------------------------------------------
 #   GMAttributesCalculator.js 2018-06-15
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2018 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
 if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
 {
 ?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('6 9={},B=0;$(1j).1i(3(){$(\'.1e\').X(3(){6 a=k h();a.Y($(4).e(\'E\'))});2(g($(".r").e(\'z\'))!=\'7\'||g($(".1d").e(\'z\'))!=\'7\'){6 b=k h();b.j();6 c=F;2($(".r").e(\'f\')==\'I\'){$(".r").1l(3(){2(c==F){6 a=k h();a.j()}})}K{$(".r").1b(3(){6 a=k h();a.j();c=5})}}$("#1a").19(3(){6 a=k h();a.j();2(g(Q)!=\'7\'){Q.18()}})});3 h(){4.j=3(){2($(".17").V==0){m.n({o:$("#G").11(),p:\'q.l?s=t&u=O\',f:"v",w:5,x:3(a){$("#13").8(a)}}).8}2(!$(\'#U a\').D(\'i-1c-12\')){2(9.i!==7){9.i.Z()}6 b=++B;9.i=m.n({o:$("#G").11(),p:\'q.l?s=t&u=10\',f:"v",w:5,x:3(a){2(b===B){$("#U").8(a)}}}).8}};4.Y=3(b,c){6 d=[],y=F;$(\'#S\'+b+\' .14\').X(3(){2($(4).e(\'f\')==\'I\'){2($(4).15(\'16\')==5){d.T(4.R+\'=\'+P(4.E));2($(4).D(\'N-i\')){y=5}}}K{d.T(4.R+\'=\'+P(4.E));2(g($(4).L(\'J:H\'))!=\'7\'&&$(4).L(\'J:H\').D(\'N-i\')==5){y=5}}});2((y==5||(g(c)!=\'7\'&&c==5))&&(d.A(\'&\').1f(/1g\\[/)!=-1||g($(\'#S\'+b+\' .1h\').e(\'z\'))!=\'7\')){2(9[\'C\'+b]!==7){9[\'C\'+b].Z()}9[\'C\'+b]=m.n({o:d.A(\'&\')+\'&M=\'+b,p:\'q.l?s=t&u=10\',f:"v",w:5,x:3(a){$(\'#1k\'+b).8(a)}}).8;2($(\'#W\'+b).V>0){m.n({o:d.A(\'&\')+\'&M=\'+b,p:\'q.l?s=t&u=O\',f:"v",w:5,x:3(a){$(\'#W\'+b).8(a)}})}}}}',62,84,'||if|function|this|true|var|undefined|html|calculator_requests|||||attr|type|typeof|GMAttributesCalculator|price|calculate|new|php|jQuery|ajax|data|url|request_port|gm_attr_calc_input|module|Attributes|action|POST|async|success|t_has_price|class|join|request_counter|price_|hasClass|value|false|cart_quantity|selected|radio|option|else|find|products_id|has|calculate_weight|escape|coo_dropdowns_listener|name|gm_add_to_cart_|push|gm_attr_calc_price|length|gm_calc_weight_|each|calculate_listing|abort|calculate_price|serialize|request|gm_calc_weight|gm_listing_form|prop|checked|details_attributes_dropdown|check_combi_status|keyup|gm_attr_calc_qty|change|on|graduated_prices_detail_row|gm_products_id|search|id|gm_graduated_prices|ready|document|gm_attr_calc_price_|click'.split('|'),0,{}));
/*<?php
 }
 else
 {
 ?>*/
var calculator_requests = {};
var request_counter = 0;

$(document).ready(function(){

	                  $('.gm_products_id').each(function(){
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate_listing($(this).attr('value'));
	                  });

	                  if(typeof($(".gm_attr_calc_input").attr('class')) != 'undefined' || typeof($(".graduated_prices_detail_row").attr('class')) != 'undefined')
	                  {
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate();

		                  var t_changed = false;

		                  if($(".gm_attr_calc_input").attr('type') == 'radio')
		                  {
			                  $(".gm_attr_calc_input").click(function()
			                                                 {
				                                                 if(t_changed == false)
				                                                 {
					                                                 var attr_calc = new GMAttributesCalculator();
					                                                 attr_calc.calculate();
				                                                 }
			                                                 });
		                  }
		                  else
		                  {
			                  $(".gm_attr_calc_input").change(function()
			                                                  { // change-event is needed for Safari 4
				                                                  var attr_calc = new GMAttributesCalculator();
				                                                  attr_calc.calculate();
				                                                  t_changed = true;
			                                                  });
		                  }
	                  }

	                  // attributes price and graduated price
	                  $("#gm_attr_calc_qty").keyup(function(){
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate();

		                  if(typeof(coo_dropdowns_listener) != 'undefined')
		                  {
			                  coo_dropdowns_listener.check_combi_status();
		                  }
	                  });
                  }
);


function GMAttributesCalculator(){

	this.calculate = function(){
		if( $(".details_attributes_dropdown").length == 0 ){
			jQuery.ajax({data: 		$("#cart_quantity").serialize(),
				            url: 		'request_port.php?module=Attributes&action=calculate_weight',
				            type: 		"POST",
				            async:		true,
				            success:	function(t_updated_weight){
					            $("#gm_calc_weight").html(t_updated_weight);
				            }
			            }).html;
		}

		// Execute the following AJAX request only if the PRICE_ON_REQUEST is disabled (refs: #41576).
		if( !$('#gm_attr_calc_price a').hasClass('price-on-request')){
			if(calculator_requests.price !== undefined){
				calculator_requests.price.abort();	
			}
			
			var count = ++request_counter;
			
			calculator_requests.price = jQuery.ajax({data: 		$("#cart_quantity").serialize(),
				            url: 		'request_port.php?module=Attributes&action=calculate_price',
				            type: 		"POST",
				            async:		true,
				            success:	function(t_updated_price){
								if(count === request_counter){
									$("#gm_attr_calc_price").html(t_updated_price);
								}
				            }
			            }).html;
		}
	};

	this.calculate_listing = function(gm_id, p_force_request){

		var inputs = [];

		var t_has_price = false;

		$('#gm_add_to_cart_' + gm_id + ' .gm_listing_form').each(function()
		                                                         {
			                                                         if($(this).attr('type') == 'radio')
			                                                         {
				                                                         if($(this).prop('checked') == true){
					                                                         inputs.push(this.name + '=' + escape(this.value));

					                                                         if($(this).hasClass('has-price'))
					                                                         {
						                                                         t_has_price = true;
					                                                         }
				                                                         }
			                                                         }
			                                                         else
			                                                         {
				                                                         inputs.push(this.name + '=' + escape(this.value));
				                                                         if(typeof($(this).find('option:selected')) != 'undefined' && $(this).find('option:selected').hasClass('has-price') == true)
				                                                         {
					                                                         t_has_price = true;
				                                                         }
			                                                         }
		                                                         }
		);

		if((t_has_price == true || (typeof(p_force_request) != 'undefined' && p_force_request == true)) && (inputs.join('&').search(/id\[/) != -1 || typeof($('#gm_add_to_cart_' + gm_id + ' .gm_graduated_prices').attr('class')) != 'undefined'))
		{
			if(calculator_requests['price_' + gm_id] !== undefined){
				calculator_requests['price_' + gm_id].abort();
			}
			
			calculator_requests['price_' + gm_id] = jQuery.ajax({data: 		inputs.join('&') + '&products_id=' + gm_id,
				                                url: 		'request_port.php?module=Attributes&action=calculate_price',
				                                type: 		"POST",
				                                async:		true,
				                                success:	function(updated_price){
					                                $('#gm_attr_calc_price_' + gm_id).html(updated_price);
				                                }
			                                }).html;

			if($('#gm_calc_weight_' + gm_id).length > 0)
			{
				jQuery.ajax({data: 		inputs.join('&') + '&products_id=' + gm_id,
					            url: 		'request_port.php?module=Attributes&action=calculate_weight',
					            type: 		"POST",
					            async:		true,
					            success: function(p_weight)
					            {
						            $('#gm_calc_weight_' + gm_id).html(p_weight);
					            }
				            });
			}

		}
	}
}
/*<?php
 }
 ?>*/