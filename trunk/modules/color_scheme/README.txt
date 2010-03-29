
Color Scheme API
------------------------

This module provides and API for other modules to generate a full color scheme with background and foreground colors based on a single color.

Usage
------------------------
  
  1. Get a valid hex color value. A color value can be validated by calling color_scheme_is_hex($hex).
  2. Use $scheme = color_scheme_create($hex) to get a color scheme.
  3. Start using the colors:
     
     Background Colors:
       $scheme->bg['-5']  <- Lightest Backround Color
       $scheme->bg['-4']
       $scheme->bg['-3']
       $scheme->bg['-2']
       $scheme->bg['-1']
       $scheme->bg['0']   <- Base Backround Color
       $scheme->bg['1']
       $scheme->bg['2']
       $scheme->bg['3']
       $scheme->bg['4']
       $scheme->bg['5']   <- Darkest Backround Color
       
     Foreground/Text Colors:
       $scheme->fg['-5']  <- Foreground color that is visible against lightest background color.
       $scheme->fg['-4']
       $scheme->fg['-3']
       $scheme->fg['-2']
       $scheme->fg['-1']
       $scheme->fg['0']   <- Foreground color that is visible against base background color.
       $scheme->fg['1']
       $scheme->fg['2']
       $scheme->fg['3']
       $scheme->fg['4']
       $scheme->fg['5']   <- Foreground color that is visible against darkest background color.
       
   4. More advanced color manipulation functions such as brighten, darken, and mix are available. See function descriptions.