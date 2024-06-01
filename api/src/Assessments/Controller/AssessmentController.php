<?php

namespace App\Assessments\Controller;

use App\Assessments\Repository\AssessmentTemplateRepository;
use App\Assessments\Repository\AssessmentPlanRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AssessmentController extends AbstractController
{

	#[Route('/plans', name: 'getAssessmentPlans', methods: ['GET'])]
	public function getAssessmentPlans(Request $request): Response
	{
		return $this->render('@assessments/plans.html.twig', [ 'navbar' => [ 'assessment_plans' => 'active' ]]);
	}

	#[Route('/plans/{identifier}', name: 'getAssessmentPlan', methods: ['GET'])]
	public function getAssessmentPlan(string $identifier, AssessmentPlanRepository $repo, Request $request): Response
	{
		$plan = $repo->findOneByIdentifier($identifier);
		return $this->render('@assessments/plan.html.twig', [ 'plan' => $plan, 'navbar' => [ 'assessment_plans' => 'active' ]]);
	}

	#[Route('/templates', name: 'getAssessmentTemplates', methods: ['GET'])]
	public function getAssessmentTemplates(Request $request): Response
	{
		return $this->render('@assessments/templates.html.twig', [ 'navbar' => [ 'assessment_templates' => 'active' ]]);
	}

	#[Route('/templates/{identifier}', name: 'getAssessmentTemplate', methods: ['GET'])]
	public function getAssessmentTemplate(string $identifier, AssessmentTemplateRepository $repo, Request $request): Response
	{
		$template = $repo->findOneByIdentifier($identifier);
		return $this->render('@assessments/template.html.twig', [ 'template' => $template, 'navbar' => [ 'assessment_templates' => 'active' ]]);
	}

}
