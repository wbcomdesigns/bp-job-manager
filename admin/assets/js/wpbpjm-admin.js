jQuery(document).ready(function(){

	var acc = document.getElementsByClassName("wpbpjm-accordion");
	var i;
	for (i = 0; i < acc.length; i++) {
		acc[i].onclick = function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.maxHeight){
				panel.style.maxHeight = null;
			} else {
				panel.style.maxHeight = panel.scrollHeight + "px";
			} 
		}
	}

	jQuery(document).on('click', '.wpbpjm-accordion', function(){
		return false;
	});
});