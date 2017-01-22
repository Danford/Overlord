/**
 * 
 */

var $selectedTile = null;

var $filterButtons = $('.filters .button');

function OnPhotoClick($photo) {

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

function CloseTile($tile) {
	AddTile($tile.attr("data-url"), $tile);
	
	$newTile = $($tile.find('#orginal').html());
	
	$(this).parent().html($newTile);
	
	setTimeout(function(){
		$('.grid').isotope('layout');
	}, 310);
}

function EditPhoto($tile) {
	if ($tile.parent().children("#edit").length <= 0) {
		AddTile($tile.attr('data-url'), $tile.parent());
	} else {
		$tile.parent().addClass("edit");
		$tile.parent().children("#edit").toggleClass("hidden");
		$tile.parent().children("#orginal").toggleClass("hidden");
	}

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
				EditPhoto($(this));
			});
			
			$('.tile #close-button').click(function () {
				CloseTile($(this).parent());
			});
			
			$tile.parent().attr("data-url", url.replace("/edit/", ""));

			$(this).find('.loading').parent().append(getImgLoadingHtml());
			
			$newTile = $replaceTile;
			
		} else {
			$newTile = $grid.prepend(data);
		}

		$grid.isotope('layout');
		$newTile.trigger('click');
	})
	.fail(function() {
		var tileFail = "<p>Error ajax failed...</p>";
		if ($replaceTile != undefined) {
			$newTile = $replaceTile.html(tileFail)
		} else {
			$newTile = $grid.prepend(tileFail);
		}
	})
	.always(function() {
		$newTile.find('.cssload-fond').toggleClass('hidden');
		$grid.isotope('layout');
		$selectedTile = $newTile;
		requestProcessing = false;
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

			$img = $tile.find('#main-img img');
			if ($img.parent().children("#full-img").length <= 0) {
				$newimg = $img.clone(true);
				
				$newimg.attr("id", "full-img");
				
				$img.addClass("hidden");
	
				$imgSrc = $img.attr("src"); 
				
				$newimg.attr("src", function (i, orgValue) {
					orgValue = orgValue.replace(".thumb", "");
					orgValue = orgValue.replace(".profileThumb", "");
					return orgValue;
				});
				
				$newimg.addClass("loading");
				$newimg = $img.before($newimg);
				$newimg.before(getImgLoadingHtml());
			} else {
				$tile.find('#main-img img').addClass("hidden");
				$tile.find('#full-img').removeClass("hidden");
			}
				
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
				TileToggle($selectedTile);
			}
		}
		
		TileToggle($(this));	
	
		setTimeout(function(){
			$grid.isotope('layout');
		}, 310);
		
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
		EditPhoto($(this));
	});
	$('.tile #close-button').click(function () {
		CloseTile($(this).parent());
	});
});
