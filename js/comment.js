$(document).ready(function() {
  //設定回報錯誤內容radio樣式
  $('input.ick').iCheck({
	    checkboxClass: 'icheckbox_minimal-green',
	    radioClass: 'iradio_minimal-green',
	    increaseArea: '20%' // optional
  });
  
  //回報錯誤確認寫入SQL
  $('#btnConfirm').on('click',function(){
	  var jsobj = {UID:parseInt($('#UID').html()),
		           CID:parseInt($('#CID').html()),
		           CategroyId:parseInt($('#CATEGROYID').html()),
		           Level:$('input:checked.ick').val()};
	  $.ajax({
		  type:'post',
		  url:'updateLevel.php',
		  data:{'formData':JSON.stringify(jsobj)},
		  datatype:'json',
		  success:function(data) {
			  getData(1);
			  $('#myModal').modal('hide');
		  }
	  }); 
  });
  
  $(".item-area").click(function(e) {
	    clearProcessSelect($(this));
	    showLodingView();
		showScore();
		clearMarquee();
	    categroyName = $(this).children()[0].children[0].innerText
		var categroyObj = {
		      "員工素質":1,
			  "清潔程度":2,
			  "住宿地點":3,
			  "房間景觀":4,
			  "餐點品質":5,
			  "舒適程度":6,
			  "設施": 7
		  }
		$('#CATEGROYID').html(categroyObj[categroyName]);
	    $('#SCORE').html(1);
	    getData(1);
	    hideLodingView();
	    
	    $(this).children().children(".progress-bar").addClass("progress-bar-striped");
	    $(this).children().children(".progress-bar").addClass("progress-bar-animated");
	    $(this).siblings().children(".item-title").children("p").children("span").removeClass("word");
	    $(this).children(".item-title").children("p").children("span").addClass("word");
	    //文字跳動
	    var words = document.querySelectorAll(".word");
	    words.forEach(function(word) {
		    var letters = word.textContent.split("");
		    word.textContent = "";
		    letters.forEach(function(letter) {
			      var span = document.createElement("span");
			      span.textContent = letter;
			      span.className = "letter";
			      word.append(span);
		    });
	    });
	    var currentWordIndex = 0;
		var maxWordIndex = words.length - 1;
	    words[currentWordIndex].style.opacity = "1";
		var rotateText = function() {
		    var currentWord = words[currentWordIndex];
		    var nextWord =
		      currentWordIndex === maxWordIndex ? words[0] : words[currentWordIndex + 1];
		    // rotate out letters of current word
		    Array.from(currentWord.children).forEach(function(letter, i) {
		      setTimeout(function() {
		        letter.className = "letter out";
		      }, i * 80);
		    });
		    // reveal and rotate in letters of next word
		    nextWord.style.opacity = "1";
		    Array.from(nextWord.children).forEach(function(letter, i) {
		      letter.className = "letter behind";
		      setTimeout(function() {
		        letter.className = "letter in";
		      }, 340 + i * 80);
	    });
	    currentWordIndex = currentWordIndex === maxWordIndex ? 0 : currentWordIndex + 1;
	  };
	  rotateText();
	  setInterval(rotateText, 2000);
  });
  
  //切換優中劣選項
  $(".score").click(function(e) {
	    showLodingView();
	    $(this).siblings().removeClass("active");
	    $(this).addClass("active");
	    $('input.ick').attr("checked","");
	    $('div.iradio_minimal-green').removeClass('checked');
	    switch($(this).html()) {
		    case "優":
		    	$('#SCORE').html(1);
		    	$('input.ick[value="優"]').attr("checked",true);
		    	$('input.ick[value="優"]').parent().addClass('checked');
		    	break;
		    case "中":
		    	$('#SCORE').html(2);
		    	$('input.ick[value="中"]').attr("checked",true);
		    	$('input.ick[value="中"]').parent().addClass('checked');
		    	break;
		    case "劣":
		    	$('#SCORE').html(3);
		    	$('input.ick[value="劣"]').attr("checked",true);
		    	$('input.ick[value="劣"]').parent().addClass('checked');
		    	break;
	    }
	    getData(1);
	    hideLodingView();
  });

  //關鍵字點選
  $('.tag').on('click',function(){
	  showLodingView();
	  clearMarquee();
	  $(this).addClass("btn-marquee");
	  $keyword =  $(this).children(0).html().substr(0,2);
	  $('#CATEGROYID').html(0);
	  $('#SCORE').html(0);
	  $(".score").each(function(){
			 $(this).hide(); 
	  });
	  getData(1,$keyword);
	  $('.item-title').removeClass("title-select");
	  hideLodingView();
  });
  
  $('#btnSearch').on('click',function(){
	  seachKeyWord();
  });
  
  $('#txtSearch').on('keydown',function(e){
	 if (e.keyCode == 13) {
		 seachKeyWord();
	 }
  });
  
  $('[data-toggle="tooltip"]').tooltip();
  
  initPage();
  
});


function gotopage(npage) {
	 getData(npage);
}

function getData(npage,keyword = "%") {
	var jsobj = {UID:parseInt($('#UID').html()),
		         NPAGE:npage,
		         CategroyId:parseInt($('#CATEGROYID').html()),
		         Score:parseInt($('#SCORE').html()),
		         Keyword:keyword};
	$.ajax({
		  type:'post',
		  url:'commentdata.php',
		  data:{'formData':JSON.stringify(jsobj)},
		  datatype:'json',
		  success:function(data) {
			  var htmlstr ="";
			  jsondata = JSON.parse(data);
			  if (jsondata.length > 0) {
				  for(index in jsondata) {
					  htmlstr += '<div class="card mb-3 mb-4">' +
				              '<div class="card-header media position-relative">' +
				              '<div class="btn-group position-absolute" style="top:10px;right:10px;text-black-50;background-color:transparent">' +
				              '<button type="button" class="reset-Button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
				              '<i class="fas fa-ellipsis-v"></i></button>'+
				              '<div class="dropdown-menu">' +
				              '<a class="dropdown-item" href="#" onclick="showModal(' + jsondata[index].CID + ');"><i class="fas fa-exclamation-triangle"></i>回報錯誤</a>' +
				              '</div></div>' +
				              '<img src="' + jsondata[index].ComImg + '" class="mr-3 rounded-circle" style="width:64px">' +
				              '<div class="media-body">' +
				              '<h5 class="mt-0 d-inline-block float-left mr-2 mt-1">'+ jsondata[index].ComName +'</h5>' +
				              '<p class="text-black-50 d-inline-block mb-0" style="font-size:15px;margin-top:6px;">發表了一則評論　' + jsondata[index].ComPublish + '</p>' +
				              '<div class="clearfix"></div></div></div>' +
				              '<div class="card-body">' +
				              '<h5 class="card-title">' + jsondata[index].ComTitle + '</h5>' +
				              '<p class="card-text" >' + jsondata[index].ComText +
				              '</div></div>';
				  }
				  $('#TOTPAGE').html(jsondata[0].TOT_PAGE);
				  $('.card-wrap').html(htmlstr);
			  }
			  else {
				  $('#TOTPAGE').html(0);
				  $('.card-wrap').html("");		 
			  }
			  if (parseInt($('#CATEGROYID').html()) == 0) {
				  $('.reset-Button').hide();
			  } 
			  CreateNav(npage,parseInt($('#TOTPAGE').html()));
		  }
	  });
}

//初始化頁面資料
function initPage() {
	getData(1);
	hideScore();
}

//搜尋關鍵字
function seachKeyWord() {
    showLodingView();
	$keyword = $('#txtSearch').val();
	$('#CATEGROYID').html(0);
	$('#SCORE').html(0);
	hideScore();
	getData(1,$keyword);
	hideLodingView();
}

//顯示讀取遮罩
function showLodingView() {
    displayProgress();
    displayMaskFrame();
}

// 隱藏讀取遮罩
function hideLodingView() {
    $('#divProgress').hide();
    $("#divMaskFrame").hide();
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

// 顯示優中劣選單
function showScore() {
	$(".score").each(function(){
		$(this).show(); 
	});
}

//隱藏優中劣選單
function hideScore() {
	$(".score").each(function(){
		$(this).hide(); 
	});
}

//清除ProcessBar選擇狀態
function clearProcessSelect(element) {
	element.siblings().removeClass("active");
	element.addClass("active");
    $(".progress-bar").removeClass("progress-bar-striped");
    $(".progress-bar").removeClass("progress-bar-animated");
}

//清除跑馬燈狀態
function clearMarquee () {
	$(".tag").each(function() {
		$(this).removeClass("btn-marquee");
	});
}

//顯示回報錯誤modal
function showModal(CID) {
	$('#CID').html(CID);
	$('#myModal').modal('show');
}

//建立分頁選項
function CreateNav(nowpage,totpage) {
    var str = new Array();

    var firstpage = (Math.floor(nowpage / 5)) * 5;
    if (nowpage % 5 != 0) {
        firstpage++;
    }
    else {
        firstpage -= 4;
    }

    str.push('<ul class="pagination">');
    //上一頁
    if (nowpage == 1) {
        str.push('<li class="prev disabled"><a href="#"><span class="fa fa-angle-left"></span>&nbsp;上一頁</a></li >')
    } else {
        str.push('<li class="prev"><a href="#" onclick="gotopage(' + (parseInt(nowpage, 10) - 1) + ');"><span class="fa fa-angle-left"></span>&nbsp;上一頁</a></li >')
    }
    
    for (var i = firstpage; i <= firstpage + 4 && i <= totpage; i++) {
        if (nowpage == i) {
            str.push('<li class="active"><a href="#">' + i + '</a></li>')
        }
        else {
            str.push('<li><a href="#" onclick="gotopage('+ i +')">' + i + '</a></li>')
        }
    }
    
    //下一頁
    if (nowpage == totpage) {
        str.push('<li class="next disabled"><a href="#"> 下一頁 &nbsp;<span class="fa fa-angle-right"></span></a></li>');
    } else {
        str.push('<li class="next"><a href="#" onclick="gotopage(' + (parseInt(nowpage, 10) + 1) + ');"> 下一頁 &nbsp;<span class="fa fa-angle-right"></span></a></li>');
    }

    str.push("</ul>")

    $('#nav').html(str.join(""));
}
