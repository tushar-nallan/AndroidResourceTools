# AndroidResourceTools

##GifToAnimationDrawable

Converts Animated GIFs to AnimationDrawables.

P.S. Web front-end for this functionality can be found at http://tusharonweb.in/AndroidResourceTools/GifToAnimationDrawable

###Pre-Requisites

- Install imagemagick
  - For OS X   - Install package from http://cactuslab.com/imagemagick/
  - For ubuntu - From terminal, run "sudo apt-get install imagemagick"

###Usage

<pre>./gif2animdraw.sh   &lt;path_to_gif&gt;   &lt;source_density&gt;   &lt;comma_seperated_target_densities_without_space&gt;

Example ./gif2animdraw.sh   giphy.gif   hdpi   ldpi,xhdpi,xxhdpi</pre>
