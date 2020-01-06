<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Travelute</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet"/>
<link href="skins/minimal/green.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="css/comment.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="js/icheck.js"></script>
<script type="text/javascript" src="js/comment.js"></script>
</head>
<body>
	<div class="wrap">
	  <div class="main-image"></div>
	  <div class="content">
	    <div class="rotating-text content-wrap pt-0 px-3 mb-2">
	      <div class="row">
			<?php 
			  include_once ('all.php');
    	      $sql = "SELECT * FROM fn_getAnalysisScore(".$_REQUEST['UID'].")";
    	      $rows = fetchDBData($sql);

	          $i = 0;
	          $bgcolor = ['','bg-info','bg-danger','','bg-success','bg-warning','bg-success'];
	          $totalScore = 0;
	          foreach ($rows as $row) {

	              echo '<div class="item-area col-md-3 pt-0 px-4 pb-0">';
	              
	              $counts = $row['avgScore'] == 0 ? 0 : $row['counts']; 
	              echo '<div class="m-2 item-title"><p class="p-0 my-2"><span>' . $row['categroyName'] .'</span></p></div>'.
    	              '<div id="pro'. $i . '"class="progress mb-2 " '.
    	              'data-toggle="tooltip" data-placement="bottom" title="點選後篩選條件[' . $row['categroyName'] . ']" data-trigger="hover">'.
    	              '<div class="progress-bar ' . $bgcolor[$i] .'" role="progressbar" style="width:' . $row['avgScore'] . 
    	              '%;" aria-valuenow="' . $row['avgScore'] . '" aria-valuemin="0" aria-valuemax="100">' . $row['avgScore'] . '分 / ' . $counts . '則</div></div></div>';

	              $totalScore += $row['avgScore'];
	              $i++;      
             }
             echo    '<div class="col-md-3 pt-0 px-4 pb-3">'.
                     '<div class="m-2 item-title">綜合評價</div>'.
    	             '<div class="star-area">';
             for($j=0; $j < 10 ; $j++) {
                 if ($j < floor($totalScore / 70))
                     echo '<span class="fa fa-star checked"></span>';
                 else 
                     echo '<span class="fa fa-star"></span>';
             }
             echo    '</div></div>';
    	      
	      
	      ?>
		  </div>
	    </div>
	    <div id="UID" style="display: none;"><?php echo $_REQUEST['UID'] ?> </div>
	    <div id="TOTPAGE" style="display: none;"></div>
	    <div id="CATEGROYID" style="display: none;">0</div>
	    <div id="SCORE" style="display: none;">0</div>
	    <div id="CID" style="display: none;">0</div>
	    <div class="content-wrap pt-0 px-3 mb-4">
	      <div class="row">
	        <div class="col-md-6">
	        <button type="button" class="tag btn btn-primary ml-2"><span data-text="浴室">浴室<i class="ml-1 far fa-plus-square"></i></span></button>
               <button type="button" class="tag btn btn-success ml-2 "><span data-text="兒童">兒童<i class="ml-2 fas fa-plus-square"></i></span></button>
               <button type="button" class="tag btn btn-danger ml-2 "><span data-text="熱水">熱水<i class="ml-2 fas fa-plus-square"></i></span></button>
               <button type="button" class="tag btn btn-info ml-2 "><span data-text="捷運">捷運<i class="ml-2 fas fa-plus-square"></i></span></button>
	        </div>
	        <div class="col-md-6 form-inline my-lg-0">
	          <button type="button" class="score btn btn-outline-primary ml-auto active">優</button>
			  <button type="button" class="score btn btn-outline-secondary ml-2">中</button>
			  <button type="button" class="score btn btn-outline-success ml-2">劣</button>
	          <input id="txtSearch" class="form-control mr-sm-2 ml-4" type="search" placeholder="Search" aria-label="Search">
	          <button id="btnSearch" class="btn btn-outline-success my-2 my-sm-0" type="button">Search</button>
	          <ol class="breadcrumb mb-0 py-2 ml-4">
                  <li class="breadcrumb-item"><a href="index.php">首頁</a></li>
                  <li class="breadcrumb-item active" aria-current="page">評論頁</li>
              </ol>
	        </div>
  
	      </div>
	    </div>
	    <div class="card-wrap"></div>
	    <div id="nav"></div>
	  </div>
	</div>
	
	<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
    	    <div class="modal-content">
    	        <div class="modal-header">
    		        <h5 class="modal-title">回報分類錯誤</h5>
    		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		          <span aria-hidden="true">&times;</span>
    		        </button>
    	        </div>
    	        <div class="modal-body">
    	        	<input class="ick" type="radio" name="level" value="優" checked>優 &nbsp;
    	        	<input class="ick" type="radio" name="level" value="中" />中 &nbsp;
    	        	<input class="ick" type="radio" name="level" value="劣" />劣 &nbsp;
    	        	
    	        </div>
    	      	<div class="modal-footer">
    	        	<button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
    	        	<button id="btnConfirm" type="button" class="btn btn-success" >確認</button>
    	        </div>
    	    </div>
    	</div>
	</div>
	
	<div id="divProgress" style="text-align:center; display: none; position: fixed; top: 50%;  left: 50%;" >
        <img id="imgLoading" src="images/loading.gif" />
        <br />
        <font color="#1B3563" size="6px">資料處理中</font>
    </div>
    <div id="divMaskFrame" style="background-color: #F2F4F7; display: none; left: 0px;
        position: absolute; top: 0px;">
    </div>
</body>
</html>