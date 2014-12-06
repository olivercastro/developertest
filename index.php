<html>
	<head>
		<title>Developer Test for Magento Application</title>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css"/>
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		
		<style>
			#search-container {
				padding-top:20px;
				padding-bottom: 10px;
			}
			#message{
				padding-top: 10px;
			}
			.trimmed-container{
				padding:0 15px 0 15px;
			}
			ul.paginator{
				margin: 0;
			}
			.navigator-paginator{
				float:right;
			}
			#result{
				margin-top: 10px;
			}
			#result .alert{
				padding:2px;
				margin: 0;
				text-align: center;
			}
			mark{
				background: transparent;
				border: 1px solid black;
			}
			#hover img{
				width: 10%;
				top: 50%;
				position: relative;
			}
			#hover{
				text-align: center;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">		  		  
			  <div class="col-lg-12">
			    <div class="input-group" id="search-container">
			      <input type="text" class="form-control" id="query">
			      <span class="input-group-btn">
			        <button class="btn btn-default" type="button" id="search">Go!</button>
			      </span>
			    </div>
			  </div>
			</div>
			<div class="row trimmed-container" id="message" style="display:none">
				<div class="alert alert-danger" role="alert">Nothing to Search</div>
			</div>
			<div class="row trimmed-container" id="results-container">

				<div class="panel panel-default">
				  <div class="panel-heading">
				    <h3 class="panel-title">Results</h3>
				  </div>
				  <div class="panel-body">
				  	<div class="row">
				  		<div class="col-lg-6">
						  	<div class="btn-group input-group">
						  		<span class="input-group-addon">Show all :</span>
			  					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="row-limiter" limiter="30">
			    					30 <span class="caret"></span>
			  					</button>
			  					<ul class="dropdown-menu" role="menu">
			    					<li><a href="#">30</a></li>
			    					<li><a href="#">40</a></li>
			    					<li><a href="#">50</a></li>
			  					</ul>
							</div>
						</div>
						<div class="col-lg-6">
							<nav class="navigator-paginator" page="1">
							  <ul class="pagination paginator">
							    <li><a href="#" class="backward"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>
							    <li><a href="#">1</a></li>
							    <li><a href="#">2</a></li>
							    <li><a href="#">3</a></li>
							    <li><a href="#">4</a></li>
							    <li><a href="#">5</a></li>
							    <li><a href="#" class="forward"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>
							  </ul>
							</nav>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="row trimmed-container">
					    <table class="table table-bordered table-striped" id="result">
					    	<thead>
					    		<td>Title</td>
					    		<td>Year</td>
					    		<td>Run Time</td>
					    	</thead>
					    	<tbody>
					    	</tbody>
					    </table>
					</div>
				  </div>
				</div>
			</div>
			<div id="progress"></div>
			<div id="raw">
			</div>
		</div>
		<script type="text/javascript">
			var host = window.location.href;
			$(document).ready(function(){
				$("#query").keypress(function(e){
					if(e.which == 13){
						$("#search").click();
					}
				});

				$("#search").click(function(){
					if($("#query").val() == ""){
						$("#message").show();
					}else{
						$("#message").hide();
						doSearch();
					}
				});//end search

				$('.btn-group .dropdown-menu li a').click(function(){
					$('#row-limiter').text($(this).text()+"  ");
					$("#row-limiter").attr("limiter",$(this).text());
					$('#row-limiter').append('<span class="caret"></span>');
					doSearch();
				});//end	

				$('nav .paginator li a').on("click",function(e){
					if($(this).children().length == 0){
						resetPaginator();
						$(this).parent().addClass("active");
						$('.navigator-paginator').attr('page',$(this).text());
						doSearch();
					}else{
						var page = parseInt($('.navigator-paginator').attr("page"));	
						if($(this).hasClass('backward')){
							if(page > 1){
								page--;
								$('.navigator-paginator').attr("page",page);
							}
						}else if($(this).hasClass('forward')){
							if(page < 5){
								page++;
								$('.navigator-paginator').attr("page",page);
							}
						}
						resetPaginator(parseInt($('.navigator-paginator').attr("page")));		
						doSearch();
					}
					e.preventDefault();
				});//end

				function resetPaginator(ndx){
					$('nav .paginator li a').each(function(index,element){
						$(this).parent().removeClass("active");
						if(typeof ndx !== 'undefined'){
							if(index == ndx)
								$(this).parent().addClass("active");
						}
					});
				}//end

				function highlightFirstOccur(needle,haystack){
					var hay = haystack;
					var find = needle;

					find = find.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
					var pattern = new RegExp("("+find+")", "i");

					hay = hay.replace(pattern,"<mark>$1</mark>");
					hay = hay.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/,"$1</mark>$2<mark>$4");

					return hay;
				}
				function doSearch(){
					var opt = new Options();
					$.ajax({
						url : host + 'result.php',
						data : { query : opt.QUERY, limiter : opt.LIMITER, page : opt.PAGE },
						success : function(resp){
							$("#result tbody").empty();
							for(var indx in resp.movies){
								var filtered = highlightFirstOccur(opt.QUERY,resp.movies[indx].title);
								var runtime = resp.movies[indx].runtime == "" ? '<div class="alert alert-info" role="alert">N/A</div>' : resp.movies[indx].runtime;
								var year = resp.movies[indx].year == "" ? '<div class="alert alert-warning" role="alert">N/A</div>' : resp.movies[indx].year;
								var elem = '<tr><td>'+filtered+'</td><td>'+year+'</td><td>'+runtime+'</td></tr>';
								$("#result tbody").append(elem);								
							}	
							$("#hover").remove();	
						},
						beforeSend : function(){
							var hover = '<div id ="hover" style="width: 100%; position: fixed; height: 100%; background: black; opacity: 0.6; z-index: 999; top: 0;">'+
									'<img src="images/loading.gif"/></div>';
							$("body").append(hover);
						},
						dataType : "json"
					});
				}
				function Options(){
					this.QUERY = $("#query").val();
					this.LIMITER = $("#row-limiter").attr("limiter");
					this.PAGE = $('.navigator-paginator').attr('page');
				}		
			});//end
		</script>
	</body>
</html>