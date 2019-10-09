(function() {
  var template = Handlebars.template, templates = OCA.External.Templates = OCA.External.Templates || {};
templates['icon'] = template({"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<li data-icon=\""
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "\">\n	<div class=\"img\">\n		<img src=\""
    + alias4(((helper = (helper = helpers.url || (depth0 != null ? depth0.url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"url","hash":{},"data":data}) : helper)))
    + "\">\n	</div>\n	<span class=\"name\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</span>\n	<span class=\"icon icon-delete\" title=\""
    + alias4(((helper = (helper = helpers.deleteTXT || (depth0 != null ? depth0.deleteTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"deleteTXT","hash":{},"data":data}) : helper)))
    + "\"></span>\n</li>\n";
},"useData":true});
templates['site'] = template({"1":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : (container.nullContext || {});

  return ((stack1 = helpers["if"].call(alias1,(helpers.isSelected || (depth0 && depth0.isSelected) || helpers.helperMissing).call(alias1,(depth0 != null ? depth0.code : depth0),(depths[1] != null ? depths[1].lang : depths[1]),{"name":"isSelected","hash":{},"data":data}),{"name":"if","hash":{},"fn":container.program(2, data, 0, blockParams, depths),"inverse":container.program(4, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "");
},"2":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.code || (depth0 != null ? depth0.code : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"code","hash":{},"data":data}) : helper)))
    + "\" selected=\"selected\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.code || (depth0 != null ? depth0.code : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"code","hash":{},"data":data}) : helper)))
    + "\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"6":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : (container.nullContext || {});

  return ((stack1 = helpers["if"].call(alias1,(helpers.isSelected || (depth0 && depth0.isSelected) || helpers.helperMissing).call(alias1,(depth0 != null ? depth0.device : depth0),(depths[1] != null ? depths[1].device : depths[1]),{"name":"isSelected","hash":{},"data":data}),{"name":"if","hash":{},"fn":container.program(7, data, 0, blockParams, depths),"inverse":container.program(9, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "");
},"7":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.device || (depth0 != null ? depth0.device : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"device","hash":{},"data":data}) : helper)))
    + "\" selected=\"selected\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"9":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.device || (depth0 != null ? depth0.device : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"device","hash":{},"data":data}) : helper)))
    + "\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"11":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : (container.nullContext || {});

  return ((stack1 = helpers["if"].call(alias1,(helpers.isSelected || (depth0 && depth0.isSelected) || helpers.helperMissing).call(alias1,(depth0 != null ? depth0.icon : depth0),(depths[1] != null ? depths[1].icon : depths[1]),{"name":"isSelected","hash":{},"data":data}),{"name":"if","hash":{},"fn":container.program(12, data, 0, blockParams, depths),"inverse":container.program(14, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "");
},"12":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.icon || (depth0 != null ? depth0.icon : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"icon","hash":{},"data":data}) : helper)))
    + "\" selected=\"selected\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"14":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.icon || (depth0 != null ? depth0.icon : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"icon","hash":{},"data":data}) : helper)))
    + "\"><img class=\"svg action delete-button\" src=\""
    + alias4(((helper = (helper = helpers.deleteIMG || (depth0 != null ? depth0.deleteIMG : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"deleteIMG","hash":{},"data":data}) : helper)))
    + "\" title=\""
    + alias4(((helper = (helper = helpers.removeSiteTXT || (depth0 != null ? depth0.removeSiteTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"removeSiteTXT","hash":{},"data":data}) : helper)))
    + "\"> "
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"16":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : (container.nullContext || {});

  return ((stack1 = helpers["if"].call(alias1,(helpers.isSelected || (depth0 && depth0.isSelected) || helpers.helperMissing).call(alias1,(depth0 != null ? depth0.type : depth0),(depths[1] != null ? depths[1].type : depths[1]),{"name":"isSelected","hash":{},"data":data}),{"name":"if","hash":{},"fn":container.program(17, data, 0, blockParams, depths),"inverse":container.program(19, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "");
},"17":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.type || (depth0 != null ? depth0.type : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"type","hash":{},"data":data}) : helper)))
    + "\" selected=\"selected\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"19":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "							<option value=\""
    + alias4(((helper = (helper = helpers.type || (depth0 != null ? depth0.type : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"type","hash":{},"data":data}) : helper)))
    + "\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"21":function(container,depth0,helpers,partials,data) {
    return " checked=\"checked\"";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<li data-site-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\">\n	<input type=\"text\" class=\"site-name trigger-save\" name=\"site-name\" value=\""
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.nameTXT || (depth0 != null ? depth0.nameTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nameTXT","hash":{},"data":data}) : helper)))
    + "\">\n	<input type=\"text\" class=\"site-url trigger-save\"  name=\"site-url\" value=\""
    + alias4(((helper = (helper = helpers.url || (depth0 != null ? depth0.url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"url","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.urlTXT || (depth0 != null ? depth0.urlTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"urlTXT","hash":{},"data":data}) : helper)))
    + "\">\n	<a class=\"icon-more\" href=\"#\"></a>\n\n	<div class=\"options hidden\">\n		<div>\n			<label>\n				<span>"

    + alias4(((helper = (helper = helpers.authTXT || (depth0 != null ? depth0.authTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"authTXT","hash":{},"data":data}) : helper)))
    + "</span>\n <input type=\"text\" class=\"site-loginurl trigger-save\"  name=\"site-loginurl\" value=\""
    + alias4(((helper = (helper = helpers.loginurl || (depth0 != null ? depth0.loginurl : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"loginurl","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.loginUrlTXT || (depth0 != null ? depth0.loginUrlTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"loginUrlTXT","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 160px;\" />\n			</label>\n	<label>\n"

    + "<input type=\"text\" class=\"site-login trigger-save\"  name=\"site-login\" value=\""
    + alias4(((helper = (helper = helpers.login || (depth0 != null ? depth0.login : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"login","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.loginTXT || (depth0 != null ? depth0.loginTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"loginTXT","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 160px;\" />\n			</label>\n	<label>\n"

    + "<input type=\"password\" class=\"site-password trigger-save\"  name=\"site-password\" value=\""
    + alias4(((helper = (helper = helpers.password || (depth0 != null ? depth0.password : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"password","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.passwordTXT || (depth0 != null ? depth0.passwordTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"passwordTXT","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 160px;\" />\n			</label>\n	<label>\n"

    + "<input type=\"text\" class=\"site-headers trigger-save\"  name=\"site-headers\" value=\""
    + alias4(((helper = (helper = helpers.headers || (depth0 != null ? depth0.headers : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"headers","hash":{},"data":data}) : helper)))
    + "\" placeholder=\""
    + alias4(((helper = (helper = helpers.headersTXT || (depth0 != null ? depth0.headersTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"headersTXT","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 160px;\" />\n			</label>\n		</div>\n\n		<div>\n			<label>\n				<span>"

    + alias4(((helper = (helper = helpers.languageTXT || (depth0 != null ? depth0.languageTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"languageTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<select class=\"site-lang trigger-save\">\n"
    + ((stack1 = helpers.each.call(alias1,(helpers.getLanguages || (depth0 && depth0.getLanguages) || alias2).call(alias1,(depth0 != null ? depth0.lang : depth0),{"name":"getLanguages","hash":{},"data":data}),{"name":"each","hash":{},"fn":container.program(1, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "				</select>\n			</label>\n		</div>\n\n		<div>\n			<label>\n				<span>"
    + alias4(((helper = (helper = helpers.groupsTXT || (depth0 != null ? depth0.groupsTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"groupsTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<input type=\"hidden\" name=\"site-groups\" class=\"site-groups\" value=\""
    + alias4((helpers.join || (depth0 && depth0.join) || alias2).call(alias1,(depth0 != null ? depth0.groups : depth0),{"name":"join","hash":{},"data":data}))
    + "\" style=\"width: 320px;\" />\n			</label>\n		</div>\n\n		<div>\n			<label>\n				<span>"
    + alias4(((helper = (helper = helpers.devicesTXT || (depth0 != null ? depth0.devicesTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"devicesTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<select class=\"site-device trigger-save\">\n"
    + ((stack1 = helpers.each.call(alias1,(helpers.getDevices || (depth0 && depth0.getDevices) || alias2).call(alias1,(depth0 != null ? depth0.device : depth0),{"name":"getDevices","hash":{},"data":data}),{"name":"each","hash":{},"fn":container.program(6, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "				</select>\n			</label>\n		</div>\n\n		<div>\n			<label>\n				<span>"
    + alias4(((helper = (helper = helpers.iconTXT || (depth0 != null ? depth0.iconTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"iconTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<select class=\"site-icon trigger-save\">\n"
    + ((stack1 = helpers.each.call(alias1,(helpers.getIcons || (depth0 && depth0.getIcons) || alias2).call(alias1,(depth0 != null ? depth0.icon : depth0),{"name":"getIcons","hash":{},"data":data}),{"name":"each","hash":{},"fn":container.program(11, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "				</select>\n			</label>\n		</div>\n\n		<div>\n			<label>\n				<span>"
    + alias4(((helper = (helper = helpers.positionTXT || (depth0 != null ? depth0.positionTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"positionTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<select class=\"site-type trigger-save\">\n"
    + ((stack1 = helpers.each.call(alias1,(helpers.getTypes || (depth0 && depth0.getTypes) || alias2).call(alias1,(depth0 != null ? depth0.type : depth0),{"name":"getTypes","hash":{},"data":data}),{"name":"each","hash":{},"fn":container.program(16, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "				</select>\n			</label>\n		</div>\n\n		<div class=\"site-redirect-box\">\n			<label>\n				<span>"
    + alias4(((helper = (helper = helpers.redirectTXT || (depth0 != null ? depth0.redirectTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"redirectTXT","hash":{},"data":data}) : helper)))
    + "</span>\n				<input type=\"checkbox\" id=\"site_redirect_"
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\" name=\"site_redirect_"
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n					   value=\"1\" class=\"site-redirect checkbox trigger-save\" "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.redirect : depth0),{"name":"if","hash":{},"fn":container.program(21, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + " />\n				<label for=\"site_redirect_"
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\">"
    + alias4(((helper = (helper = helpers.noEmbedTXT || (depth0 != null ? depth0.noEmbedTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"noEmbedTXT","hash":{},"data":data}) : helper)))
    + "</label>\n			</label>\n		</div>\n\n		<div class=\"button delete-button\">"
    + alias4(((helper = (helper = helpers.removeSiteTXT || (depth0 != null ? depth0.removeSiteTXT : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"removeSiteTXT","hash":{},"data":data}) : helper)))
    + "</div>\n	</div>\n</li>\n";
},"useData":true,"useDepths":true});
})();
