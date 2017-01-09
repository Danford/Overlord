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

var requestProcessing = false;
function AddTile(name, url) {

	// do not allow multiple request to process at the same time.
	if (requestProcessing == true)
		return;

	requestProcessing = true;
	$newTile = $('<div id="'+ name +'" class="grid-item grid-item--large tile destructable"><h2>'+ name +'</h2><div align="center" class="cssload-fond"><div class="cssload-container-general"><div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div><div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div><div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div><div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div></div></div></div>');
	$('.grid').prepend($newTile).isotope('reloadItems').isotope({ sortBy: 'original-order' });

	$newTile.trigger('click');
	
	var jqxhr = $.get(url + "?ajax", function() {
		
	})
	.done(function(data) {
		$newTile.append(data);
	})
	.fail(function() {
		$newTile.append("Error ajax failed...")
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

$(function() {
	$grid = $('.grid').isotope({
		// options
		layoutMode: 'packery',
		itemSelector: '.grid-item',
		stamp: '.stamp',
		
		percentPosition: true,
		stager: 30,
	});

	
	// add css loading spinner after all tile images with the loading class
	$('.tile img.loading').after("<div align='center' class='cssload-fond'><div class='cssload-container-general'><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_1'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_2'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_3'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_4'></div></div></div></div>");
	
	updateFilterCounts();
	
	// store filter for each group
	var filters = {};
	
	//recalculate grid layout on image load
	$grid.imagesLoaded().progress( function () {
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
		$tile.toggleClass("selected");
		$tile.toggleClass('grid-item');
		$tile.toggleClass('stamp');
		$tile.toggleClass('stamp--focus');
		

		if (!$tile.hasClass("selected")) {
			$tile.removeClass('stamp--full-screen');
		}
		
		if ($tile.hasClass('stamp')) {
			$grid.isotope("stamp", $tile);
		} else {
			$grid.isotope("unstamp", $tile);
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
		
		if ($(this).hasClass('selected')) {
			$(this).toggleClass('stamp--full-screen');
			$grid.isotope('layout');
			return;
		}
		
		if ($selectedTile != null) {
			if ($selectedTile.hasClass('destructable')) {
				$selectedTile.remove();
			} else {		
				TileToggle($selectedTile);
			}
		}
	
		TileToggle($(this));	
	
		$grid.isotope('layout');
	
		$selectedTile = $(this);
	});
});
