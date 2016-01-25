/**
 * Array of posts types to index
 * @type @exp;opSouVars@pro;postTypesToIndex
 */
var postTypesToIndex = opSouVars.postTypesToIndex || {};
/**
 * Array of post types with theirs total posts to index
 * @type @exp;opSouVars@pro;totalPublishedPosts
 */
var totalPublishedPosts =  opSouVars.totalPublishedPosts;
/**
 * Count total posts indexed
 * @type Number
 */
var totalPostsIndexed = 0;
/**
 * Count total posts indexed by post type
 * @type Array
 */
var totalPostsPerTypeIndexed = {};

var indexPosts = function ( postType, offset ){
	offset = offset || 0;
	var data = { indexPostType: postType, action: opSouVars.ajaxIndexAction, queryOffset: offset, runIndex: 1, _ajax_nonce_index: opSouVars.ajaxIndexNonce };
	showProgress( postType, totalPublishedPosts[postType], offset, opSouVars.labels.indexing + opSouVars.labels.postsLabels[postType] );
	jQuery.ajax({
			url: opSouVars.ajaxUrl,
			data: data,
			dataType: 'json',
			type: 'POST',
			success: function(response, textStatus) {
				if(typeof(response) === 'object' && response.error === true){
					showError( data.opSouErrorMessage );
				}else if( typeof(response) === 'object' && response !== null )
				{
					if ( response['totalIndexed'] ) {
						totalPostsIndexed += parseInt( response['totalIndexed'] );
						totalPostsPerTypeIndexed[postType] += parseInt( response['totalIndexed'] );
					}
					// If something was indexed try with the next page
					if ( parseInt( response['totalIndexed'] ) > 0 ) {
						indexPosts( postType, parseInt( offset ) + parseInt( opSouVars.postsPerPage ) );
					} else {
						var nextPostToIndex = getNextPostTypeToIndex( postType );
						if( nextPostToIndex ){
							indexPosts( nextPostToIndex, 0 );
						}
						else{
							processFinished();
						}
					}
				}else{
					showError( opSouVars.labels.indexationError );
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				try {
					errorMsg = JSON.parse(jqXHR.responseText).message;
				} catch (e) {
					errorMsg = jqXHR.responseText;
				}
				showError(errorMsg);
			}
		}
	);
};
var processFinished = function ( ){
	// Indexation is finished so we need to enable the index button
	jQuery('#opSou_index').removeAttr("disabled");
	spinnerHide();
};

function progress( percent, elementId ) {
	percent =  Math.floor( percent );
	jQuery( elementId ).progressbar( "value", percent );
}

function showProgress( type, totalItems, offset, label ) {
	var pbarId = '#progressBar_' + type;
	var pbarHtmlId = 'progressBar_' + type;
	if( offset === 0 ){
		var progressBar = '<li>' + label + '<br> <div id="'+pbarHtmlId+'"><div id="'+pbarHtmlId+'Label" class="progress-label">' + opSouVars.labels.starting + '</div></div></li>';
		jQuery( "#op-sou-index-result" ).append( progressBar );
		var progressBarObj = jQuery( pbarId );
		var progressLabel = jQuery( pbarId + 'Label' );

		progressBarObj.progressbar({
									value: false,
									change: function() {
										progressLabel.text( progressBarObj.progressbar( "value" ) + "%" );
									},
									complete: function() {
										progressLabel.text( opSouVars.labels.complete );
									}
								});
	}
	var percetange =  0;

	if( offset >= totalItems ){
		percetange = 100;
	}else{
		percetange =  ( ( offset * 100 ) / totalItems );
	}
	progress( percetange, pbarId );
}

function showError( message , postType ){
	jQuery( "#op-sou-index-error" ).empty().append( message );
}

function clearMessages(){
	jQuery( "#op-sou-index-error" ).empty();
	jQuery( "#op-sou-index-result" ).empty();
}

function indexationSectionShow(){
	jQuery( ".index-action-row" ).show();
}
function indexationSectionHide(){
	jQuery( ".index-action-row" ).hide();
}

function spinnerShow(){
	jQuery( ".spinner" ).show();
}
function spinnerHide(){
	jQuery( ".spinner" ).hide();
}


function getNextPostTypeToIndex( currentType ){
	var index = jQuery.inArray( currentType, postTypesToIndex );
	if( index !== -1 ){
		index += 1;
		if( index < postTypesToIndex.length ){
			return getPostTypeToIndex( index );
		}
	}
	return '';
}


function getPostTypeToIndex( index ){
	if( index >= 0 ){
		if( postTypesToIndex.length > 0 && typeof postTypesToIndex[index] !== 'undefined' ){
			return postTypesToIndex[index];
		}
	}
	return '';
}

(function($) {

	$(document).ready(function(){

		jQuery("#opSoutabs").tabs();

		jQuery('#opSou_appName' ).on('change', function(e){
			clearMessages();
			indexationSectionHide();
			if( jQuery( '.op-sou-set-index-name' ).length <= 0 ){
				jQuery( '.index-action-row.index-messages' ).after('<tr class="op-sou-set-index-name"><td colspan="2"><p>' + opSouVars.labels.indexNameChanged + '</p></td></tr>');
			}
		});

		//下载模版
		jQuery('#opSou_dlTemplate').on('click', function(e) {
			var action = window.location.href + '&opSou_dlTemplate=1';
			$('#opSou_form').attr('action', action);//.submit();
		});

		jQuery('#opSou_index').on( 'click', function(e){
			e.preventDefault();
			clearMessages();

			jQuery('#opSou_index').attr("disabled", true);
			jQuery("#op-sou-index-result").append(
				'<li><strong>' + opSouVars.labels.running + '</strong></li>'
			);
			jQuery('.algolia-action-button').width( jQuery('#opSou_index').outerWidth() + 30 );
			spinnerShow();

			var firstIndex = getPostTypeToIndex( 0 );

			if( firstIndex ) {
				indexPosts( firstIndex );
			}
		} );
	});
})(jQuery);
