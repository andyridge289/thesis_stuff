<?php

class Tree
{
	private $id;
	private $name;
	private $type;
	private $children;
	
	function Tree($name, $type, $count)
	{
		$this->name = $name;
		$this->type = $type;
		$this->id = $count;
		
		$this->children = array();
	}
	
	function addChild($name, $type, $count)
	{
		if(array_key_exists($name, $this->children))
		{
			// If it's already there, do nothing
		}
		else
		{
			array_push($this->children, new Tree($name, $type, $count));
		}
	}
	
	function addChildTo($name, $type, $count, $parent)
	{
		if(strcmp($parent, $this->name) === 0)
		{
			// Then we need to add it to this one
			$this->addChild($name, $type, $count);
		}
		else
		{
			// Check if its one of the children
			foreach($this->children as $child)
			{
				$child->addChildTo($name, $type, $count, $parent);
			}
		}
	}
	
	function printJSON()
	{
		$output = "{
		id: \"$this->id\",
		name: \"$this->name\",
		data: {
			type: \"$this->type\"
		},
		children: [";
		
		for($i = 0; $i < count($this->children); $i++)
		{
			$output .= $this->children[$i]->printJSON();
			
			if($i < count($this-> children) - 1)
				$output .= ",";
		}
		
		$output.= "]}";
		
		return $output;
	}
}

?>