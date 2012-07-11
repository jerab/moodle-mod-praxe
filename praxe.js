var praxe = {

	setscheduletimeend : function(name,value) {
		var type = name.substring(name.indexOf('['),name.indexOf(']')+1);
		var el = document.getElementsByName('timeend'+type);
		if(el && type != 'minute') {
			el[0].value = value;
		}
	}
}