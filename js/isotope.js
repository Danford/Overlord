/**
 * 
 */

var $selectedTile = null;

var $filterButtons = $('.filters .button');

var $openComments = null;
var imgId = 0;

function OnPhotoClick($photo) {
	$img = $photo.find('#main-img img');
	if ($img.parent().children("#full-img").length <= 0) {
		$newimg = $img.clone(true);
		
		$newimg.attr("id", "full-img");
		$newimg.attr("class", "full-img-" + imgId);
		$newimg.attr("onload", "$('.main-img-"+ imgId +"').addClass('hidden'); $('.full-img-"+ imgId +"').removeClass('hidden'); ImageLoaded(this)");

		$img.addClass("main-img-" + imgId);
		
		$imgSrc = $img.attr("src"); 

		imgId++;
		
		
		$newimg.attr("src", function (i, orgValue) {
			orgValue = orgValue.replace(".thumb", "");
			orgValue = orgValue.replace(".profileThumb", "");
			return orgValue;
		});
		
		$newimg.addClass("loading");
		$newimg = $img.before($newimg);
		$newimg.before(getImgLoadingCoverHtml());
	} else {
		$photo.find('#main-img img').addClass("hidden");
		$photo.find('#full-img').removeClass("hidden");
	}
}

function OnWritingClick($writing) {
	$writing.children('#excerpt').toggleClass('hidden');
	$writing.children('#full').toggleClass('hidden');
}

function OnGroupClick($group) {

}

function OnAboutMeClick($aboutme) {
	$aboutme.children('#excerpt').toggleClass('hidden');
	$aboutme.children('#full').toggleClass('hidden');
}

function OnExpandCommentClick($button) {
	$button.toggleClass('hidden'); 
	$button.parent().find('#add-comment').toggleClass('hidden');
	
	if ($openComments !== null) {
		$openComments.find('#add-comment').toggleClass('hidden');
		$openComments.find('#expand-comment').toggleClass('hidden');
	}
	
	$openComments = $button.parent();
}

// flatten object by concatting values
function concatValues( obj ) {
  var value = '';
  for ( var prop in obj ) {
    value += obj[ prop ];
  }
  return value;
}

function updateFilterCounts()  {
  // get filtered item elements
  var itemElems = $grid.isotope('getFilteredItemElements');
  var $itemElems = $( itemElems );
  $filterButtons.each( function( i, button ) {
    var $button = $( button );
    var filterValue = $button.attr('data-filter');
    if ( !filterValue ) {
      // do not update 'any' buttons
      return;
    }
    var count = $itemElems.filter( filterValue ).length;
    $button.find('.filter-count').text( '(' + count +')' );
  });
}

// add css loading spinner after all tile images with the loading class
function getImgLoadingHtml() {
	return "<div align='center' class='cssload-fond'><div class='cssload-container-general'><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_1'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_2'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_3'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_4'></div></div></div></div>";
}

function getImgLoadingCoverHtml() {
	return "<div align='center' class='cssload-fond cssload-fond-cover'><div class='cssload-container-general'><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_1'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_2'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_3'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_4'></div></div></div></div>";
}

function CloseTile($tile) {
	AddTile($tile.attr("data-url"), $tile);
}

function EditPhoto($tile) {
	if ($tile.children("#edit").length <= 0) {
		AddTile($tile.find("#config-button").attr('data-url'), $tile);
	} else {
		$tile.addClass("edit");
		$tile.children("#edit").toggleClass("hidden");
		$tile.children("#orginal").toggleClass("hidden");
	}
}

function EditWriting($tile) {
	if ($tile.children("#edit").length <= 0) {
		AddTile($tile.find('#config-button').attr('data-url'), $tile);
	} else {
		$tile.addClass("edit");
		$tile.children("#edit").toggleClass("hidden");
		$tile.children("#orginal").toggleClass("hidden");
	}
}

function ClickConfigButton($button) {
	$tile = $button.parent();
	if ($tile.hasClass('photo'))
		EditPhoto($tile);
	else if ($tile.hasClass('writing'))
		EditWriting($tile);

	setTimeout(function(){
		$('.grid').isotope('layout');
	}, 310);
}

var requestProcessing = false;
function AddTile(url, $replaceTile) {

	// do not allow multiple request to process at the same time.
	if (requestProcessing == true)
		return;

	requestProcessing = true;
	
	$grid = $('.grid');
	
	var jqxhr = $.get(url + "?ajax", function() {
		
	})
	.done(function(data) {
		if ($replaceTile != undefined) {
			$tile = $($($.parseHTML(data)).html());
			$replaceTile.html($tile);
			
			$('.tile #config-button').click(function () {
				ClickConfigButton($(this));
			});
			
			$('.tile #close-button').click(function () {
				CloseTile($(this).parent());
			});
			
			$replaceTile.attr("data-url", url.replace("/edit/", ""));

			$(this).find('.loading').parent().append(getImgLoadingHtml());
			
			$newTile = $replaceTile;
			
		} else {
			$newTile = $(data);
			$grid.prepend($newTile);
		}

		$newTile.trigger('click');
	})
	.fail(function() {
		var tileFail = "<p>Error ajax failed... Url: " + url + "</p>";
		
		if ($replaceTile != undefined) {
			$replaceTile.prepend(tileFail);
		} else {
			$newTile = $("<div class='grid-item tile error'>" + tileFail + "</div>");
			$grid.prepend($newTile);
		}
	})
	.always(function() {
		$newTile.find('.cssload-fond').toggleClass('hidden');
		$grid.isotope('reloadItems').isotope({ sortBy: 'original-order' });
		$grid.isotope('layout');
		$selectedTile = $newTile;
		requestProcessing = false;
				
		tinymce.remove();
    	tinymce.init({selector:'textarea'});
	});
}

function AjaxPostData(data, $replaceElement) {
	// do not allow multiple request to process at the same time.
	if (requestProcessing == true)
		return;

	requestProcessing = true;
	
	$grid = $('.grid');
	
	var jqxhr = $.post("/", data, function() {
		
	})
	.done(function(data) {
		if ($replaceElement != undefined) {
			
		}
	})
	.fail(function() {
		
	})
	.always(function() {
		
	});
}

function FillTile(response) {
	$newTile.append(response);
}

function ReplaceTileWith() {}

$(function() {
	$grid = $('.grid').isotope({
		// options
		layoutMode: 'packery',
		itemSelector: '.grid-item',
		stamp: '.stamp',
		
		percentPosition: true,
		stager: 30,
	});

	
	$('img.loading').parent().append(getImgLoadingHtml());

	// store filter for each group
	var filters = {};

	updateFilterCounts();
	
	//recalculate grid layout on image load
	$grid.imagesLoaded().progress( function ($img) {
		
		$grid.isotope('layout');
	});
	
	$('.filters').on( 'click', '.button', function() {
	  var $this = $(this);
	  // get group key
	  var $buttonGroup = $this.parents('.button-group');
	  var filterGroup = $buttonGroup.attr('data-filter-group');
	  // set filter for group
	  filters[ filterGroup ] = $this.attr('data-filter');
	  // combine filters
	  var filterValue = concatValues( filters );
	  // set filter for Isotope
	  $grid.isotope({ filter: filterValue });
	  updateFilterCounts();
	});
	
	//sort items on button click
	$('.sort-by-button-group').on( 'click', 'button', function() {
		var sortByValue = $(this).attr('data-sort-by');
		$grid.isotope({ sortBy: sortByValue });
	});
	
	//change is-checked class on buttons
	$('.button-group').each( function( i, buttonGroup ) {
	  var $buttonGroup = $( buttonGroup );
	  $buttonGroup.on( 'click', 'button', function() {
	    $buttonGroup.find('.is-checked').removeClass('is-checked');
	    $(this).addClass('is-checked');
	  });
	});
	
	function TileToggle($tile) {
		
		if ($tile.hasClass('ignore-click'))
			return;
		
		$tile.toggleClass("selected");
		$tile.toggleClass('grid-item');
		$tile.toggleClass('stamp');
		$tile.toggleClass('stamp--focus');
		
		if ($tile.hasClass('stamp')) {
			$grid.isotope("stamp", $tile);	
		} else {
			$grid.isotope("unstamp", $tile);

			$tile.find('#main-img img').removeClass("hidden");
			$tile.find('#full-img').addClass("hidden");
		}
		
		if ($tile.hasClass('photo')) {
			OnPhotoClick($tile);
		} else if ($tile.hasClass('writing')) {
			OnWritingClick($tile);
		} else if ($tile.hasClass('group')) {
			OnGroupClick($tile);
		} else if ($tile.hasClass('about-me')) {
			OnAboutMeClick($tile);
		}
	}
	
	$('.tile').click(function () {
		if ($selectedTile != null) {
			if ($selectedTile.hasClass('destructable')) {
				$selectedTile.remove();
			} else {		
				$selectedTile.addClass("smooth-trans-bottom");
				TileToggle($selectedTile);
			}
		}

		$(this).addClass("smooth-trans-top");
		TileToggle($(this));
		$this = $(this);
		setTimeout(function($this){
			$grid.isotope('layout');
			$('.smooth-trans-top').removeClass("smooth-trans-top");
			$('.smooth-trans-bottom').removeClass("smooth-trans-bottom");
		}, 320);
		
		$selectedTile = $(this);
	});
	
	
	
	$('.tile #expand-button').click(function () {
		if ($(this).parent().hasClass('selected')) {
			$(this).parent().toggleClass('stamp--full-screen');
			setTimeout(function(){
				$grid.isotope('layout');
			}, 310);
			return;
		}
	});
	
	$('.tile #config-button').click(function () {
		ClickConfigButton($(this));
	});
	
	$('.tile #close-button').click(function () {
		CloseTile($(this).parent());
	});
	
	$('.tile .profile').mouseenter(function () {
		var pos = $(this).offset();
		
		$newFriendDetailTile = $(this).find('#details').clone(true);
		$newFriendDetailTile.css("left", pos.left);
		$newFriendDetailTile.css("top", pos.top);
		$newFriendDetailTile.toggleClass("hidden");
		
		$newFriendDetailTile.mouseleave(function () {
			$(this).remove();
		});
		
		$(document).find('content').prepend($newFriendDetailTile);
	});
	
	$('.tile #expand-comment').click(function () {
		OnExpandCommentClick($(this));
	});

	$('.tile #like-button').click(function () {
		  $form = $(this).find("form");
		  
		  var url = $form.attr("action");
			 
		  // Send the data using post
		  var posting = $.post(url, $form.serialize() );
		 
		  // Put the results in a div
		  posting.done(function( data ) {
		    var content = $( data ).find( "#content" );
		    $( "#result" ).empty().append( content );
		  });
	});
	
	$('.tile #is-liked-button').click(function () {
		OnLikeClick($(this));
	});
});
