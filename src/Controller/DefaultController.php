<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Survey;
use App\Entity\User;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        if($user != 'anon.') { //Filtro para los usuarios registrados 

            $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
            $newarray = $repositorySurvey->findAll(); //Selecciona todas las encuestas del repositorio
            $usedsurvey = $user->getSurveys(); //Selecciona todas las encuestas del usuario logueado
            $numberused = count($usedsurvey);
            $numbersurvey = count($newarray);
            $all_survey = array();
            for ($x=0; $x<$numbersurvey; $x++) {
                $comparation = false;
                for ($y=0; $y<$numberused; $y++) {
                    if ($newarray[$x] == $usedsurvey[$y]) { //Compara cada resultado del usuario con las encuestas existentes
                        $comparation = true;
                    }
                }
                if ($comparation == false) { // si no hay conincidencias guarda este objeto en un nuevo array
                    array_push($all_survey, $newarray[$x]);
                }
            }

            if(isset($_GET['surveyId'])) { //si recibe esta variable
                $survey = new Survey();
                $survey = $repositorySurvey->findOneById($_GET['surveyId']);
                $all_surveyQuestion = $survey->getQuestions();
                $contador = 0; // Este contador es para diferenciar los name de las preguntas y las respuestas y poder saber que nombre tiene cada uno //
                
                    return $this->render('default/index.html.twig', [
                    'user'=>$user, 'all_survey'=>$all_survey, 'survey'=>$survey, 'all_surveyQuestion'=>$all_surveyQuestion, 'contador'=>$contador //muestra al usuario la encuestas con sus preguntas 
                                                                                                                                                //y un contador que diferencia el nombre de las preguntas para almacenar las respuestas
                    ]);

             } else {

                return $this->render('default/index.html.twig', ['user'=>$user, 'all_survey'=>$all_survey // muestra el array de las encuestas que aun no ha contestado al usuario.
                ]);

             }
            
            
            
        } else { // en este caso no se filtran las  encuestas se muestran todas una vez en la vista este usuario no puede enviar las respuestas(input submit oculto)//

            $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
            $all_survey = $repositorySurvey->findAll();
      

                if(isset($_GET['surveyId'])) {
                    $survey = new Survey();
                    $survey = $repositorySurvey->findOneById($_GET['surveyId']);
                    $all_surveyQuestion = $survey->getQuestions();
                    $contador = 0; // Este contador es para diferenciar los name de las preguntas y las respuestas y poder saber que nombre tiene cada uno //
                    
                        return $this->render('default/index.html.twig', [
                        'user'=>$user, 'all_survey'=>$all_survey, 'survey'=>$survey, 'all_surveyQuestion'=>$all_surveyQuestion, 'contador'=>$contador
                        ]);

                } else {

                    return $this->render('default/index.html.twig', [
                    'user'=>$user, 'all_survey'=>$all_survey
                    ]);
                }
        }
    }

    /**
     * @Route("/administracion", name="administracion")
     */
    public function administracion()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
        $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);
        $all_survey = $repositorySurvey->findAll(); // obtenemos todas las encuestas
        $all_question = $repositoryQuestion->findAll(); // obtenemos todas las preguntas

        $nall_survey = count($all_survey);
        $comparationarray = array();

        for ($x=0; $x<$nall_survey; $x++) { // añadimos las preguntas de cada encuesta a un array
            $aux = $all_survey[$x]->getQuestions();
            $naux = count($aux);
                for ($z=0; $z<$naux; $z++) {
                    array_push($comparationarray, $aux[$z]);
                }
        }

        $nall_questions = count($all_question);
        $finalarray = array();

        for ($y=0; $y<$nall_questions; $y++)  { 
            $check = false;
           foreach($comparationarray as $compare) {
               if($all_question[$y]->getId() == $compare->getId()) { //comparamos las preguntas de todas las encuestas con todas las preguntas existentes
                   $check = true;
               }
           }
           if($check == false) { // si no se encuentran resultados se añade la pregunta aun nuevo array y lo mandamos a la vista.
            array_push($finalarray, $all_question[$y]);
           }
        }


        
        return $this->render('default/administracion.html.twig', [
            'user' => $user, 'all_survey'=>$all_survey, 'all_question'=>$all_question, 'finalarray'=>$finalarray
        ]);
   
    }

    /**
     * @Route("/estadisticas", name="estadisticas")
     */    

    public function estadisticas()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
        $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);
        $repositoryAnswer = $this->getDoctrine()->getRepository(Answer::class);
        $repositoryUser = $this->getDoctrine()->getRepository(User::class);
        $all_survey = $repositorySurvey->findAll(); //recoge todas las encuestas
        $all_user = $repositorySurvey->findAll();   //recoge todo los usuarios 

        if(isset($_POST['selectedsurvey'])) { //cuando selecciona una encuesta
            $survey = new Survey();
            $survey = $repositorySurvey->findOneById($_POST['surveySelected']);// busca las encuesta selecionada
            $surveyQuestions = $survey->getQuestions(); //sacamos todas preguntas
            $surveyed = count($survey->getUserequest()); //contaomos el numero de usuarios que contestaton la encuesta
            $nUser = count($all_user); //cuenta todos los usuarios
            $percentageuser = round((($surveyed * 100) / $nUser)); //hace la media de los usuarios que han contestado respecto al total de usuarios existentes

            $booleanQuestions = array();
            $textQuestions = array();
            $dateQuestions = array();

            foreach ($surveyQuestions as $thisQuestion) { // filtramos las preguntas por tipos
                if($thisQuestion->getType() == "boolean") {
                    array_push($booleanQuestions, $thisQuestion);
                }
                if($thisQuestion->getType() == "text") {
                    array_push($textQuestions, $thisQuestion);
                }
                if($thisQuestion->getType() == "date") {
                    array_push($dateQuestions, $thisQuestion);
                }
            }

            $resultsBoolean = array();

            if(!empty($booleanQuestions)) { // de las preguntas boleanas sacamos sus estadisticas

                foreach ($booleanQuestions as $thisBoolean) {
                   $trueAnswers = $repositoryAnswer->findTrue($thisBoolean->getId());
                   $falseAnswers = $repositoryAnswer->findFalse($thisBoolean->getId());
                   $nTrueAnswers = count($trueAnswers);
                   $nFalseAnswers = count($falseAnswers);
                   $percentageTrue = null;
                   $percentageFalse = null;
                   if ($surveyed != 0) { //si no participa gente no hace el porcentage ya que da error
                   $percentageTrue = round((($nTrueAnswers * 100) / $surveyed));
                   $percentageFalse = round((($nFalseAnswers * 100) / $surveyed));
                    }
                   $results = new \stdClass(); //este objeto recoje los datos necesarios a mostrar
                   $results->questionboolean = $thisBoolean->getThequest();
                   $results->ntrue = $nTrueAnswers;
                   $results->nfalse = $nFalseAnswers;
                   $results->pertrue = $percentageTrue;
                   $results->perfalse = $percentageFalse;

                   array_push($resultsBoolean, $results); //se añaden los objetos con las diferentes estadisticas a un array
                }

            }

            
            
            if(!empty($dateQuestions)) { //filtro para sacar la media de edad que participa en la encuesta segun una pregunta especifica
                foreach ($dateQuestions as $thisDate) {
                    if($thisDate->getThequest() == '¿Fecha de nacimento?') {
                        $agesCount = 0;
                        $AnswerAge = $repositoryAnswer->findByQuestion($thisDate->getId());
                        foreach ($AnswerAge as $thisAnswer) { //recorre todas las fechas de nacimiento hasta obtener todas la suma de todas las edades
                           $bornDate = $thisAnswer->getTheAnswer();
                           $dato = explode("-", $bornDate, 3);
                           $thisYear = date("Y");
                           $thisAge = $thisYear - $dato[0];
                           $agesCount = $agesCount + $thisAge;
                        }
                    }
                }
                $nAnswers = count($AnswerAge);// cuenta el numero de respuestas y calcula la media de las edades
                $averageAge = $agesCount / $nAnswers;

                return $this->render('default/estadisticas.html.twig', [ //vista si existe media de edad
                    'user' => $user, 'all_survey'=>$all_survey, 'survey'=>$survey, 'surveyed'=>$surveyed, 'resultsBoolean'=>$resultsBoolean, 'percentageuser'=>$percentageuser, 'averageAge'=>$averageAge, 'textQuestions'=>$textQuestions, 'dateQuestions'=>$dateQuestions, 'booleanQuestions'=>$booleanQuestions
                ]);
            } else {

            return $this->render('default/estadisticas.html.twig', [ //vista para todo tipo de respuestas
                'user' => $user, 'all_survey'=>$all_survey, 'survey'=>$survey, 'surveyed'=>$surveyed, 'resultsBoolean'=>$resultsBoolean, 'percentageuser'=>$percentageuser, 'textQuestions'=>$textQuestions, 'dateQuestions'=>$dateQuestions, 'booleanQuestions'=>$booleanQuestions
                ]);
            }
        } else {
            return $this->render('default/estadisticas.html.twig', [ //vista sin respuestas solo para seleccionar la encuesta deseada
                'user' => $user, 'all_survey'=>$all_survey, 
            ]);
        }
    }
    /**
     * @Route("/makeSurvey", name="makeSurvey")
     */

    public function makeSurvey()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        if(isset($_POST['newSurvey'])) { //en este metodo creamos una encuesta y la subimos a la base de datos
            $survey = new Survey();
            $survey->setTitle($_POST['title']);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($survey);
            $manager->flush($survey);
        }

        return $this->redirectToRoute('administracion'); // nos devuelve a administracion
   
    }

     /**
     * @Route("/makeQuestion", name="makeQuestion")
     */

    public function makeQuestion()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        if(isset($_POST['newQuestion'])) { //aqui creamos objetos de la entidad pregunta y la subimos a la base de datos
            $question = new Question();
            $question->setThequest($_POST['question']);
            $question->setType($_POST['typeAnswer']);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($question);
            $manager->flush($question);
        }

        return $this->redirectToRoute('administracion');
   
    }

    /**
     * @Route("/addQuestion", name="addQuestion")
     */
   
    public function addQuestion()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        
        $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
        $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);

        if(isset($_POST['confirmSurvey'])) { //relacionamos una pregunta con una encuesta buscandolas por id

            $theSurvey = $repositorySurvey->findOneById($_POST['theSurvey']);
            $entityManager = $this->getDoctrine()->getManager();
            if(!empty($_POST['thequestions'])) {
                foreach($_POST['thequestions'] as $checked) {

                    $thisQuestion = $repositoryQuestion->findOneById($checked);
                    $theSurvey->addQuestion($thisQuestion);
                    $entityManager->merge($theSurvey);
                    $entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('administracion');
    }

    /**
     * @Route("/addAnswer", name="addAnswer")
     */

    public function addAnswer()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
       
        if(isset($_POST['env'])) { //En este metodos se añaden a la base de datos las respuestas y

            $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
            $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);
            $entityManager = $this->getDoctrine()->getManager();

            for($x=0; $x<$_POST['contador']; $x++) { //con el contador hemos dado nombre a las variables id de pregunta y las repuestas junto a una R
                $thisQuestion = new Question();
                $thisQuestion = $repositoryQuestion->findOneById($_POST[''.$x.'']); //buscamos la pregunta por el id
               
                $answer = new Answer(); //Nuevo objeto respuesta
                $answer->setTheAnswer($_POST['R'.$x.'']);//agregamos la respuesta
                $answer->setQuestion($thisQuestion);//agregamos la relacion a la pregunta
                $entityManager->persist($answer);
                $entityManager->flush($answer);
            }

                $thisSurvey = $repositorySurvey->findOneById($_POST['encuesta']); //relaciona el usuario con la encuesta contestada
                $user->addSurvey($thisSurvey);
                $entityManager->merge($user);
                $entityManager->flush();
        }

        return $this->redirectToRoute('index'); //volvemos al index
        
    }

    /**
     * @Route("/showTypeText", name="showTypeText")
     */

    public function showTypeText()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
       
        if(isset($_POST['selectedQuestion'])) { // mostramos las respuestas de la pregunta tipo texto seleccionada
            $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
            $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);

            $thisQuestion = $repositoryQuestion->findOneById($_POST['questionSelected']);

            $survey = new Survey();
            $survey = $repositorySurvey->findOneById($_POST['surveySelected']);

            $all_answer = $thisQuestion->getAnswerRelation();

            return $this->render('default/estadisticas.html.twig', [
                'user' => $user, 'all_answer'=>$all_answer, 'thisQuestion'=>$thisQuestion, 'survey'=>$survey
            ]);
        }

        return $this->redirectToRoute('estadisticas');
        
    }

    /**
     * @Route("/showTypeDate", name="showTypeDate")
     */

    public function showTypeDate()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
       
        if(isset($_POST['selectedQuestion'])) { // mostramos las respuestas de la pregunta tipo fecha seleccionada
            $repositorySurvey = $this->getDoctrine()->getRepository(Survey::class);
            $repositoryQuestion = $this->getDoctrine()->getRepository(Question::class);

            $thisQuestionDate = $repositoryQuestion->findOneById($_POST['questionSelected']);

            $survey = new Survey();
            $survey = $repositorySurvey->findOneById($_POST['surveySelected']);

            $all_answer = $thisQuestionDate->getAnswerRelation();

            return $this->render('default/estadisticas.html.twig', [
                'user' => $user, 'all_answer'=>$all_answer, 'thisQuestion'=>$thisQuestionDate, 'survey'=>$survey
            ]);
        }

        return $this->redirectToRoute('estadisticas');
        
    }
}
