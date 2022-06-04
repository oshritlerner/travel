months = dir('data');
months = {months.name}';

for month = 1:length(months)
    if strcmp(months{month}, '.') || strcmp(months{month}, '..')
        continue;
    end
    days = dir(['data/', months{month}]);
    days = {days.name}';
    for day = 1:length(days)
        if strcmp(days{day}, '.') || strcmp(days{day}, '..')
            continue;
        end
        copyfile(['data/', months{month}, '/' days{day}, '/index.html'],  ['txts/', months{month}, days{day}, '.txt']);
    end
end

