l = (data) ->
  console.log data

animationLength = 200

mainContent = $('#main-content')

jQuery(document).ready ($) ->

  $.getScript "http://tools.novaramedia.com/tool/novara_live/schedule.min.js", ->
    if isLive()
      $("#livenow").css {
        height: 20
        paddingTop: 8
        paddingBottom: 8
      }
    setInterval (->
      if isLive()
        $("#livenow").css {
          height: 20
          paddingTop: 8
          paddingBottom: 8
        }
      return
    ), 15000
    return

  layout()
  $(window).resize ->
    layout()


  $('.js-toggle-drawer').click ->
    $('#drawer-main').slideToggle(animationLength)

  $('.js-toggle-tags').click ->
    $('#drawer-tags').slideToggle(animationLength)

  $('.masonry').each ->
    t = $(this)
    $(this).imagesLoaded ->
      t.masonry()

layout = ->
  windowHeight = $(window).height()
  headerHeight = $('#header').outerHeight(true)
  footerHeight = $('#footer').outerHeight(true)
  mainContent.css {
    'min-height': (windowHeight-headerHeight-footerHeight)+'px'
  }