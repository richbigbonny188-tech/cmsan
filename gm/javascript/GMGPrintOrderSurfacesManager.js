/* GMGPrintOrderSurfacesManager.js <?php
#   --------------------------------------------------------------
#   GMGPrintOrderSurfacesManager.js2018-06-15
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('2 1B(f){1.3=U 1F();1.8=\'\';1.l=0;1.n=f;1.V=2(a,b,c,d){9 e=U 1j(d,1);1.3[d]=e;1.3[d].1k(b);1.3[d].1A(c);1.3[d].u(a);1.k(d)};1.S=2(b){9 c,y,h,1D,O=N(b);Y.Z({10:\'12=13&16=S&17=\'+O,18:\'19.1a\',1b:\'1c\',1e:"1f",1g:1h,1i:2(a){c=a}});r(c.l!=\'0\'){1.I();1l(9 d 1m c.3){r(h==1n){h=d}1.V(c.3[d].8,c.3[d].1v,c.3[d].1z,d);1.o(d);y=c.3[d].1J;1.3[d].1C(y)}1.u(c.8);1.k(h);1.o(h);1.H()}1.i()};1.H=2(){9 b=1;$(\'#w\'+1.4()+\' .j, #w\'+1.4()+\' .7\').11(2(){9 a=$(1).X(\'p\');a=a.14(/15/g,\'\');M=N(a);b.k(M);b.o(b.C())})};1.o=2(a){$(\'#w\'+1.4()+\' .7\').D(2(){$(1).E(\'7\');$(1).F(\'j\')});$(\'#m\'+1.4()+\' .G\').D(2(){$(1).1d()});r($(\'#6\'+a).X(\'p\')!=\'6\'+a){$(\'#5\'+1.4()).J(\'<K L="7" p="6\'+1.3[a].B()+\'"><q>\'+1.3[a].A()+\'</q></K>\');$(\'#m\'+1.4()).J(\'<P L="G" p="Q\'+1.3[a].B()+\'" 1o="1p: 1q; 1r: 1s; 1t: \'+1.3[a].1u()+\'R; 1w: \'+1.3[a].1x()+\'R;"></P>\')}1y{$(\'#6\'+a).E(\'j\');$(\'#6\'+a+\' q\').x(1.3[a].A());$(\'#6\'+a).F(\'7\');$(\'#Q\'+a).i()}$(\'#5\'+1.4()+\' .j\').T(2(){$(1).t({\'z-s\':\'1E\'})});$(\'#5\'+1.4()+\' .7\').T(2(){$(1).t({\'z-s\':\'W\'})});$(\'#5\'+1.4()+\' .j\').1G(2(){$(1).t({\'z-s\':\'W\'})})};1.I=2(){$(\'#5\'+1.4()).x(\'\');$(\'#m\'+1.4()).x(\'\')};1.i=2(){$(\'#5\'+1.4()).i();$(\'#m\'+1.4()).i()};1.u=2(a){1.8=1H(a)};1.A=2(){v 1.8};1.k=2(a){1.l=a};1.C=2(){v 1.l};1.1I=2(a){1.n=n};1.4=2(){v 1.n}}',62,108,'|this|function|v_surfaces|get_surfaces_groups_id|gm_gprint_tabs_|tab_|gm_gprint_tab_active|v_name|var||||||||t_first_surfaces_id|show|gm_gprint_tab|set_current_surfaces_id|v_current_surfaces_id|gm_gprint_content_|v_surfaces_groups_id|display_surface|id|span|if|decoration|css|set_name|return|order_surfaces_groups_id_|html|coo_elements|text|get_name|get_surfaces_id|get_current_surfaces_id|each|removeClass|addClass|gm_gprint_surface|activate_tabs|reset_display|append|li|class|c_clicked_surfaces_id|gm_gprint_clear_number|c_surfaces_groups_id|div|surface_|px|load_surfaces_group|mouseover|new|load_surface|none|attr|jQuery|ajax|data|click|module|GPrintOrder|replace|gm_gprint_tab_|action|surfaces_groups_id|url|request_port|php|dataType|json|hide|type|GET|async|false|success|GMGPrintOrderSurfaces|set_width|for|in|null|style|overflow|hidden|position|relative|width|get_width|v_width|height|get_height|else|v_height|set_height|GMGPrintOrderSurfacesManager|load_elements|c_product|underline|Object|mouseout|gm_unescape|set_surfaces_groups_id|v_elements'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMGPrintOrderSurfacesManager(p_surfaces_groups_id)
{
    this.v_surfaces = new Object();
    this.v_name = '';
    this.v_current_surfaces_id = 0;
    this.v_surfaces_groups_id = p_surfaces_groups_id;

    this.load_surface = function(p_name, p_width, p_height, p_surfaces_id)
	{
		var coo_surface = new GMGPrintOrderSurfaces(p_surfaces_id, this);

        this.v_surfaces[p_surfaces_id] = coo_surface;
        this.v_surfaces[p_surfaces_id].set_width(p_width);
        this.v_surfaces[p_surfaces_id].set_height(p_height);
        this.v_surfaces[p_surfaces_id].set_name(p_name);

        this.set_current_surfaces_id(p_surfaces_id);
	}

    this.load_surfaces_group = function(p_surfaces_groups_id)
	{
        var coo_surfaces_group;
		var coo_elements;
		var t_first_surfaces_id;
		var c_product;
		var c_surfaces_groups_id = gm_gprint_clear_number(p_surfaces_groups_id);

		jQuery.ajax({
            data: 'module=GPrintOrder&action=load_surfaces_group&surfaces_groups_id=' + c_surfaces_groups_id,
            url: 'request_port.php',
            dataType: 'json',
            type: "GET",
            async: false,
            success: function(p_surfaces_group)
			{
				coo_surfaces_group = p_surfaces_group;
			}
        });

		if(coo_surfaces_group.v_current_surfaces_id != '0')
		{
			this.reset_display();

			for(var t_surfaces_id in coo_surfaces_group.v_surfaces)
			{
				if(t_first_surfaces_id == null)
				{
					t_first_surfaces_id = t_surfaces_id;
				}

				this.load_surface(coo_surfaces_group.v_surfaces[t_surfaces_id].v_name, coo_surfaces_group.v_surfaces[t_surfaces_id].v_width, coo_surfaces_group.v_surfaces[t_surfaces_id].v_height, t_surfaces_id);
				this.display_surface(t_surfaces_id);

				coo_elements = coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements;
				this.v_surfaces[t_surfaces_id].load_elements(coo_elements);
			}

			this.set_name(coo_surfaces_group.v_name);
			this.set_current_surfaces_id(t_first_surfaces_id);
			this.display_surface(t_first_surfaces_id);

			this.activate_tabs();
		}

		this.show();
    }

	this.activate_tabs = function()
	{

		var coo_surfaces_manager_copy = this;

		$('#order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab, #order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').click(function()
		{
			var f_clicked_surfaces_id = $(this).attr('id');
            f_clicked_surfaces_id = f_clicked_surfaces_id.replace(/gm_gprint_tab_/g, '');

            c_clicked_surfaces_id = gm_gprint_clear_number(f_clicked_surfaces_id);

            coo_surfaces_manager_copy.set_current_surfaces_id(c_clicked_surfaces_id);
            coo_surfaces_manager_copy.display_surface(coo_surfaces_manager_copy.get_current_surfaces_id());
        });
    }

    this.display_surface = function(p_surfaces_id)
	{
        $('#order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').each(function()
		{
            $(this).removeClass('gm_gprint_tab_active');
            $(this).addClass('gm_gprint_tab');
        });

        $('#gm_gprint_content_' + this.get_surfaces_groups_id() + ' .gm_gprint_surface').each(function()
		{
            $(this).hide();
        });

        if($('#tab_' + p_surfaces_id).attr('id') != 'tab_' + p_surfaces_id)
		{
            $('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).append('<li class="gm_gprint_tab_active" id="tab_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '"><span>' + this.v_surfaces[p_surfaces_id].get_name() + '</span></li>');
            $('#gm_gprint_content_' + this.get_surfaces_groups_id()).append('<div class="gm_gprint_surface" id="surface_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '" style="overflow: hidden; position: relative; width: ' + this.v_surfaces[p_surfaces_id].get_width() + 'px; height: ' + this.v_surfaces[p_surfaces_id].get_height() + 'px;"></div>');
        }
        else
		{
            $('#tab_' + p_surfaces_id).removeClass('gm_gprint_tab');
			$('#tab_' + p_surfaces_id + ' span').html(this.v_surfaces[p_surfaces_id].get_name());
			$('#tab_' + p_surfaces_id).addClass('gm_gprint_tab_active');
			$('#surface_' + p_surfaces_id).show();
        }

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'underline'
			});
		});

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab').mouseout(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});
    }

	this.reset_display = function()
	{
		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).html('');
		$('#gm_gprint_content_' + this.get_surfaces_groups_id()).html('');
	}

	this.show = function()
	{
		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).show();
		$('#gm_gprint_content_' + this.get_surfaces_groups_id()).show();
	}

	this.set_name = function(p_name)
	{
		this.v_name = gm_unescape(p_name);
	}

	this.get_name = function()
	{
		return this.v_name;
	}

    this.set_current_surfaces_id = function(p_surfaces_id)
	{
        this.v_current_surfaces_id = p_surfaces_id;
    }

    this.get_current_surfaces_id = function()
	{
        return this.v_current_surfaces_id;
    }

    this.set_surfaces_groups_id = function(p_surfaces_groups_id)
	{
        this.v_surfaces_groups_id = v_surfaces_groups_id;
    }

    this.get_surfaces_groups_id = function()
	{
        return this.v_surfaces_groups_id;
    }
}
/*<?php
}
?>*/

