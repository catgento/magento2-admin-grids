<?php declare(strict_types=1);

namespace Catgento\AdminGrids\Model\DataType;

use Hyva\Admin\Api\DataTypeGuesserInterface;
use Hyva\Admin\Api\DataTypeInterface;
use Hyva\Admin\Model\DataType\DataTypeToStringConverterLocatorInterface;
use Magento\Catalog\Api\CategoryAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\CategoryFactory;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;

use function array_map as map;
use function array_merge as merge;
use function array_slice as slice;

/**
 * This data type can not be automatically determined, it must be configured as the column type in the grid
 */
class CategoryNameDataType implements DataTypeInterface
{
    const TYPE_ARRAY = 'array';
    const LIMIT = 5;

    /**
     * @var \Hyva\Admin\Model\DataType\DataTypeToStringConverterLocatorInterface
     */
    private $toStringConverterLocator;

    /**
     * @var \Hyva\Admin\Api\DataTypeGuesserInterface
     */
    private $dataTypeGuesser;
    private CategoryAttributeRepositoryInterface $categoryAttributeRepository;
    private CategoryFactory $categoryResourceFactory;
    private CategoryAttributeInterface $nameAttribute;

    public function __construct(
        DataTypeToStringConverterLocatorInterface $toStringConverterLocator,
        DataTypeGuesserInterface $dataTypeGuesser,
        CategoryAttributeRepositoryInterface $categoryAttributeRepository,
        CategoryFactory $categoryResourceFactory
    ) {
        $this->toStringConverterLocator = $toStringConverterLocator;
        $this->dataTypeGuesser          = $dataTypeGuesser;
        $this->categoryAttributeRepository = $categoryAttributeRepository;
        $this->categoryResourceFactory = $categoryResourceFactory;
    }

    public function valueToTypeCode($value): ?string
    {
        return is_array($value) ? self::TYPE_ARRAY : null;
    }

    public function typeToTypeCode(string $type): ?string
    {
        return $type === self::TYPE_ARRAY ? $type : null;
    }

    public function toString($value): ?string
    {
        $categoryResource = $this->categoryResourceFactory->create();
        $nameAttribute = $this->categoryAttributeRepository->get('name');

        foreach ($value as $categoryId) {
            $categoryNames[] = $categoryResource->getAttributeRawValue(
                $categoryId,
                $nameAttribute,
                0
            );
        }

        return $this->valueToTypeCode($value)
            ? (empty($value) ? '[ ]' : $this->implode($categoryNames, 1))
            : null;
    }

    public function toHtmlRecursive($value, $maxRecursionDepth = 1): ?string
    {
        return $this->valueToTypeCode($value)
            ? $this->implode($value, $maxRecursionDepth)
            : $this->toString($value);
    }

    private function mayRecurse(int $depth): bool
    {
        return $depth <= self::UNLIMITED_RECURSION || $depth > 0;
    }

    private function implode(array $value, $maxRecursionDepth): string
    {
        return empty($value)
            ? '[ ]'
            : '[' . ($this->mayRecurse($maxRecursionDepth)
                ? implode(', ', $this->recur($value, $maxRecursionDepth))
                : '...') . ']';
    }

    private function recur(array $value, int $maxRecursionDepth): array
    {
        $overLimit   = count($value) > self::LIMIT;
        $itemsToShow = $overLimit ? slice($value, 0, self::LIMIT) : $value;

        $strings = map(function ($value) use ($maxRecursionDepth): string {
            $converter = $this->toStringConverterLocator->forTypeCode($this->dataTypeGuesser->valueToTypeCode($value));
            return $converter->toHtmlRecursive($value, $maxRecursionDepth - 1);
        }, $itemsToShow);

        return merge($strings, ($overLimit ? ['...'] : []));
    }
}
