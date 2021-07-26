#!/usr/bin/env bash

FirstArray=(
"\/\*PackageCommentStart\*\/"
"\/\*PackageCommentEnd\*\/"
"{{--PackageBladeCommentStart--}}"
"{{--PackageBladeCommentEnd--}}"
)
SecondArray=(
"\/\*PackageCommentStart"
"PackageCommentEnd\*\/"
"{{--PackageBladeCommentStart"
"PackageBladeCommentEnd--}}"
)

tag=0
for i in "${FirstArray[@]}"
do
	find . -name "*.php" -exec sed -i "s@${FirstArray[$tag]}@${SecondArray[$tag]}@g" '{}' \;
	find . -name "*.js" -exec sed -i "s@${FirstArray[$tag]}@${SecondArray[$tag]}@g" '{}' \;
	tag=$((tag+1))
done