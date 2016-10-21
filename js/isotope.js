/**
 * 
 */

var $selectedTile = null;

var $filterButtons = $('.filters .button');


function OnPhotoClick($photo) {
	$photo.toggleClass('grid-item--large');
}

function OnWritingClick($writing) {
	$writing.toggleClass('grid-item--large');
	$writing.children('#excerpt').toggleClass('hidden');
	$writing.children('#full').toggleClass('hidden');
	$grid.isotope('layout');
}

function OnGroupClick($group) {
	
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
		itemSelector: '.grid-item',
		stamp: '.stamp',
		
		percentPosition: true,
		stager: 30,
		getSortData: {
			title: '#title',
			date: '[data-date]',
			category: '[data-category]'
		},
		
		masonry: {
			columnWidth: '.grid-sizer'
		},
	});

	// add css loading spinner after all tile images with the loading class
	$('.tile > img.loading').after("<div align='center' class='cssload-fond'><div class='cssload-container-general'><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_1'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_2'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_3'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_4'></div></div></div></div>");
	
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
	
	$('.tile').click(function () {
		if ($selectedTile != null && $selectedTile != $(this)) {
	
			if ($selectedTile.hasClass('destructable'))
				$selectedTile.remove();
			else
			{		
				if ($selectedTile.hasClass('photo')) {
					OnPhotoClick($selectedTile);
				} else if ($selectedTile.hasClass('writing')) {
					OnWritingClick($selectedTile);
				} else if ($selectedTile.hasClass('group')) {
					OnGroupClick($selectedTile);
				}
			}
		}
	
		if ($selectedTile != $(this)) {
			if ($(this).hasClass('photo')) {
				OnPhotoClick($(this));
			} else if ($(this).hasClass('writing')) {
				OnWritingClick($(this));
			} else if ($(this).hasClass('group')) {
				OnGroupClick($(this));
			}
	
			$grid.isotope('layout');
		}
	
		$selectedTile = $(this);
	});
});
