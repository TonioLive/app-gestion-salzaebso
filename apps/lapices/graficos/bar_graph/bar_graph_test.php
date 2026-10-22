<?php
/**************************************************
 Scott Mattocks
 mttcks@hotmail.com
 Example usage script
 This class is a work in progress and is not 100%
 ready for use.  The included class creates a PNG
 image bar graph for inclusion into HTML pages.  
 The graph can display one or more data set in 
 user defined colors.  The font size, font color
 background color, spacing between sets of bars,
 labels, and y-axis tick marks are all set by the
 user.  To use the class you first need to collect
 some information, such as the data, labels and 
 the image dimensions.  While the thickness and 
 height of the bars will adjust to fit your image,
 the text will not.  After creating the image you
 can set many of the features.  You must becareful
 to give the right font size to prevent text from
 overlapping.  
***************************************************/

 include('bar_graph.class');

 // Create values for the graph
 $values = array(array(15, 39, 23, 18), array(20, 30, 22, 34), array(12, 12, 12, 12));
 
 // Create labels for each value
 $labels = array('June', 'July', 'Aug', 'Sept');
 
 // Distance between tick marks on y-axis
 $interval = 10;

 // Color to display bars in.  
 // The number of colors must match the number of data sets
 $bar_color1 = array(99, 33, 99); // dark purple
 $bar_color2 = array(200, 10, 10); // dark red
 $bc = array($bar_color1, $bar_color2, array(23, 200, 100));

 // Create the graph object
 // bar_graph( int width, int height, string x-axis label, string y-axis label,
 //            array bar-grouping labels, int space between tick marks,
 //            int space between bars, array data set(s) )
 $bg = new bar_graph(200, 150, 'Months', '# of Sandwiches', $labels, $interval, 5, $values);

 // Set up the graph
 $bg->set_font_size(8);
 $bg->set_bg_color(array(0,0,0));
 $bg->set_font_color(array(255,255,255));
 $bg->set_bar_color($bc);
 
 // Create a key.  
 // The number of key labels must match the number of data sets.
 // The first label will be assigned to the first data set etc...
 $bg->key(array('PBJ', 'Ham', 'Turkey'));
 $bg->graph();

?>