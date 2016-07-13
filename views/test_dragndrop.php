<html>
    <head>
        <meta charset="utf-8">
	
        <script type="text/javascript" language="javascript" src="views/lib/jquery.js"></script>
        <script type="text/javascript" language="javascript" src="views/lib/jquery-ui-1.8.18.custom.min.js"></script>
        
	<style>
	#draggable { background-color: yellow; width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; }
	#droppable { background-color: greenyellow; width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px; }
	</style>
	<script>
	$(function() {
		$( "#draggable" ).draggable();
                $( "#opt1" ).draggable();
		$( "#droppable" ).droppable({
			drop: function( event, ui ) {
				$( this )
                                    .addClass( "ui-state-highlight" )
                                    .find( "p" )
                                    .html( "Dropped!" );
			}
		});
	});
	</script>
    </head>
    <body>
        <div class="demo">
	
        <div id="draggable" class="ui-widget-content">
                <p>Drag me to my target</p>
        </div>
            
            <select id="lista" size="2">
                <option id="opt1" value="aaa">aaa</option>
                <option id="opt2" value="bbb">bbb</option>
            </select>

        <div id="droppable" class="ui-widget-header">
                <p>Drop here</p>
        </div>

        </div><!-- End demo -->

        <div class="demo-description">
        <p>Enable any DOM element to be droppable, a target for draggable elements.</p>
        </div><!-- End demo-description -->
    </body>
</html>