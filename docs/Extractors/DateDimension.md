# Collection

Creates a Date Dimension table for a data warehouse

```php
$etl->extract('date_demension', $iterable, $options);
```

## Options

### Columns
Columns from the iterable item that will be extracted.

| Type | Default value |
|----- | ------------- |
| array | `null` |

```php
$options = ['columns' => ['DateKey', 'DateFull', 'Year', 'Month', 'DayOfMonth']];
```

Currently, the default columns are:

 * DateKey
 * DateFullName
 * DateFull
 * Year
 * Quarter
 * QuarterName
 * QuarterKey
 * Month
 * MonthKey
 * MonthName
 * DayOfMonth
 * NumberOfDaysInTheMonth
 * DayOfYear
 * WeekOfYear
 * WeekOfYearKey
 * ISOWeek
 * ISOWeekKey
 * WeekDay
 * WeekDayName
 * IsWorkDayKey

### StartDate
First day in date dimesnion table.

| Type | Default value |
|----- | ------------- |
| array | `5YearsAgo` |

```php
$options = ['startDate' => '2015-01-01T60:00:00-4'];
```

### EndDate
First day in date dimesnion table.

| Type | Default value |
|----- | ------------- |
| array | `+5Years` |

```php
$options = ['startDate' => '2025-01-01T06:00:00+4'];
```
