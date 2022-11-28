<?php
declare(strict_types=1);

namespace RLTSquare\LogEntryAndEmail\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use RLTSquare\LogEntryAndEmail\Model\Config;

/**
 * Class Router
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->logger   = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {   
        $identifier = trim($request->getPathInfo(), '/');

        if (strpos($identifier, 'rltsquare') !== false) {
            
            /* Log string to rltsquare.log file */
            $this->logger->debug("page visited");    

            /* Send email to admin */
            $this->sendEmail();

            $request->setModuleName('rltsquare_logentryandemail');
            $request->setControllerName('index');
            $request->setActionName('index');
            
            return $this->actionFactory->create(Forward::class, ['request' => $request]);
        }

        return null;
    }

    /**
     * Send Mail
     * 
     */
    public function sendEmail()
    {   
        $templateId = $this->config->getEmailTemplate();
        $fromEmail  = $this->config->getSenderEmail();  
        $fromName   = $this->config->getSenderName();   
        $toEmail    = $this->config->getReceiverEmail();

        try {
            $storeId = $this->storeManager->getStore()->getId();
 
            $from = ['email' => $fromEmail, 'name' => $fromName];
            
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars([])
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}