<?php



class ClassDeclaration
{

}

abstract class CorrectClassDeclarationWithExtends extends correctClassDeclaration
{

}

final  class CorrectClassDeclarationWithImplements implements correctClassDeclaration
{

}


// Incorrect placement of opening/closing braces, including indent.
class IncorrectBracePlacement {}
class IncorrectBracePlacementWithExtends extends correctClassDeclaration {}
class IncorrectBracePlacementWithImplements implements correctClassDeclaration {}


    class IncorrectIndentedClass
    {

    }//end class


// Incorrect code placement for opening/closing brace.
class IncorrectCodeAfterOpeningBrace
{ echo phpinfo();

}//end class

class IncorrectCodeAfterClosingBrace
{

} echo phpinfo();


class IncorrectCodeBeforeClosingBrace
{

echo phpinfo(); }

    class IncorrectIndentedClass
{

}

class ClassOne implements ClassTwo, ClassThree
{
}//end class

class ClassOne implements ClassFour   ,ClassFive,  ClassSix
{
}//end class

class CorrectClassDeclarationWithExtends extends correctClassDeclaration

{

}

class CorrectClassDeclarationWithExtends   extends correctClassDeclaration
{

}//end class

class   CorrectClassDeclaration
{

}//end class


class CorrectClassDeclaration extends CorrectClassDeclaration2 implements ICorrectClassDeclaration
{

}//end class

interface MyInterface
{

}
?>

<?php

class CorrectClassDeclaration
{

}//end class
