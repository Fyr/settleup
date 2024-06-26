<?php

class My_Navigation_Container extends Zend_Navigation_Container
{
	public function findByIndex($index)
	{
		$iterator=new RecursiveIteratorIterator($this,RecursiveIteratorIterator::SELF_FIRST);
		
		$i=0;
		foreach ($iterator as $page)
		{
			if ($i===(int)$index)
			{
				return $page;
			}
			$i++;
		}
		
		return null;
	}
	
	public function findOneBy($property,$value=null, $useRegex = false)
	{
		if (is_array($property)&&$value===null)
		{
			$iterator=new RecursiveIteratorIterator($this,RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ($iterator as $page)
			{
				$flag=true;
				foreach ($property as $name => $value)
				{
					if ($page->get($name)!=$value)
					{
						$flag=false;
						break;
					}
				}
				
				if ($flag)
				{
					return $page;
				}
			}
			
			return null;
		}
		else
		{
			return parent::findOneBy($property,$value,$useRegex);
		}
	}
	
	public function findAllBy($property, $value, $toArray=false)
	{
		$found=array();
		
		$iterator=new RecursiveIteratorIterator($this,RecursiveIteratorIterator::SELF_FIRST);
		
		foreach ($iterator as $page)
		{
			if ($page->get($property)==$value)
			{
				$found []=$toArray ? $page->toArray() : $page;
			}
		}
		
		return $found;
	}

    public function removePageRecursive(Zend_Navigation_Page $page)
    {
        if ($this->removePage($page)) {
            return true;
        }

        $iterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $pageContainer) {
            if ($pageContainer->removePage($page)) {
                return true;
            }
        }

        return false;
    }
}
