<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_ProductQuestions
 * @copyright   Copyright (c) 2019 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */

namespace Ecomteck\ProductQuestions\Controller\Adminhtml\Question;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Post
 * @package Ecomteck\ProductQuestions\Controller\Adminhtml\Question
 */
class Post extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecomteck_ProductQuestions::question_save';

    /**
     * @var \Ecomteck\ProductQuestions\Model\QuestionFactory
     */
    private $questionFactory;

    /**
     * @var \Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface
     */
    private $questionRepository;


    public function __construct(
        Action\Context $context,
        \Ecomteck\ProductQuestions\Model\QuestionFactory $questionFactory = null,
        \Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface $questionRepository = null
    ) {
        $this->questionFactory = $questionFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Ecomteck\ProductQuestions\Model\QuestionFactory::class);
        $this->questionRepository = $questionRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Post action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            /** @var \Ecomteck\ProductQuestions\Model\Question $model */
            $model = $this->questionFactory->create();
            $model->setData($data);

            try {
                $this->questionRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the question.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the question.'));
            }
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/');
    }
}
