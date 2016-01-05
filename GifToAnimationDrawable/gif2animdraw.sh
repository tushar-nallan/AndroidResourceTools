gif=$1;
filename="${gif%.*}";
echo "Filename is $filename"
density=$2;
targetDensities=$3;

function getScaleForDensity() {
  case "$1" in
    ("ldpi") echo 0.75 ;;
    ("mdpi") echo 1 ;;
    ("hdpi") echo 1.5 ;;
    ("xhdpi") echo 2 ;;
    ("xxhdpi") echo 3 ;;
    ("xxxhdpi") echo 4 ;;
  esac
}

declare -a allowed_densities=("ldpi" "mdpi" "hdpi" "xhdpi" "xxhdpi" "xxxhdpi")
valid_density=false;
for i in "${allowed_densities[@]}"; do
  if [ "$density" == "$i" ]; then
    valid_density=true;
  fi
   # or do whatever with individual element of the array
done

if [ $valid_density == false ]; then
  density="hdpi";
fi
echo "Density is $density"

mkdir drawable drawable-$density;

cd drawable-$density;

pwd

prevdirsize=-1;
curdirsize=`ls -1| wc -l`;
echo "prev=$prevdirsize current=$curdirsize";
for (( i=0; prevdirsize<curdirsize ; i++ )); do
	prevdirsize=$curdirsize;
	start=$(($i*5));
	end=$(($start+4));
	convert -coalesce ../$gif[$start-$end] $filename\_%d.png;
	curdirsize=`ls -1| wc -l`
	echo "prev=$prevdirsize current=$curdirsize";
done

n=$curdirsize;

cd ../drawable;

pwd

echo -e '<animation-list xmlns:android="http://schemas.android.com/apk/res/android" android:oneshot="true">' > $filename.xml

for (( i=0; i<n; i++ )); do
	echo '    <item android:drawable="@drawable/'$filename\_$i'" android:duration="60"/>' >> $filename.xml;
done

echo -e '</animation-list>' >> $filename.xml;

cd ..

refdens=$(getScaleForDensity $density)

echo reference density is $refdens

for i in $(echo $targetDensities | sed "s/,/ /g")
do
    if [ $i == $density ]; then
        continue;
    fi
    mkdir drawable-$i; cd drawable-$i;
    curdens=$(getScaleForDensity $i)
    echo current density is $curdens
    rp=`echo $curdens $refdens | awk '{printf "%.2f", $1/$2}'`
    rp=`echo $rp 100 | awk '{printf "%d", $1*$2}'`
    echo rp is $rp in float n is $n;
    for (( j=0; j<n; j++ )); do
        convert ../drawable-$density/$filename\_$j.png -resize $rp\% $filename\_$j.png;
    done
    cd ..
done

zip -r $filename.zip *

rm -rf drawable*  $gif;
