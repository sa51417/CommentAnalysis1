$(function() {
	  $(".search-area").click(function(e) {
	    $("body").addClass("search-area-active");
	  });

	  $(document).mouseup(function(e) {
	    var _con = $(".search-area");
	    if (!_con.is(e.target) && _con.has(e.target).length === 0) {
	      $("body").removeClass("search-area-active");
	    }
	  });
	  
	  var pickerStart = new Pikaday({
	    field: document.getElementById("datepicker-start"),
	    firstDay: 1,
	    minDate: new Date(),
	    maxDate: new Date(2020, 12, 31),
	    yearRange: [2000, 2020]
	  });
	  var pickerEnd = new Pikaday({
	    field: document.getElementById("datepicker-end"),
	    firstDay: 1,
	    minDate: new Date(),
	    maxDate: new Date(2020, 12, 31),
	    yearRange: [2000, 2020]
	  });
	  
	  
	  $("#main").submit(function(){
			if ($('#url').val() == "") {
				$('#myModal').modal('show');
				return false;
			}
	  });
				  
	  $("#myModal").on("hidden.bs.modal",function(e){
		  $("body").addClass("search-area-active");
	  });
		  
	  $('.list-item').click(function(e) {
		  e.preventDefault();
		  if ($(this).find('a').attr('href') != undefined) {
			  ShowProgressBar();
			  $('#url').val($(this).find('a').attr('href'));
			  $('#main').submit();
		  }
	  });
	  
	  $('#url').keypress(function(e){
		  if (e.keycode = 13) {
			  ShowProgressBar();
			  $('#main').submit();
		  }
	  });
	  
	// 顯示讀取遮罩
	  function ShowProgressBar() {
	      displayProgress();
	      displayMaskFrame();
	  }

	  // 隱藏讀取遮罩
	  function HideProgressBar() {
	      var progress = $('#divProgress');
	      var maskFrame = $("#divMaskFrame");
	      progress.hide();
	      maskFrame.hide();
	  }
	  // 顯示讀取畫面
	  function displayProgress() {
	      var w = $(document).width();
	      var h = $(window).height();
	      var progress = $('#divProgress');
	      progress.css({ "z-index": 999999, "top": (h / 2) - (progress.height() / 2), "left": (w / 2) - (progress.width() / 2) });
	      progress.show();
	  }
	  // 顯示遮罩畫面
	  function displayMaskFrame() {
	      var w = $(window).width();
	      var h = $(document).height();
	      var maskFrame = $("#divMaskFrame");
	      maskFrame.css({ "z-index": 999998, "opacity": 0.7, "width": w, "height": h });
	      maskFrame.show();
	  }
});

