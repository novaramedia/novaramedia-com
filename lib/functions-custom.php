<?php

// for array_filter

function onlyTopLevelCategoryFilter($var) {
  if ($var->category_parent == 0) {
    return true;
  }
}